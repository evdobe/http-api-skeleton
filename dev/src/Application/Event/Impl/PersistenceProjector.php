<?php

namespace Application\Event\Impl;

use Application\Event\Projector;
use Application\Event\Store;
use Application\Persistence\Manager;
use Domain\Event;
use Domain\EventApplyFailedEvent;
use Domain\Exception\EventApplyException;
use Domain\MyAggregate;

class PersistenceProjector implements Projector
{
    public function __construct(protected Store $store, protected Manager $manager)
    {
        $this->manager->open();
    }

    public function project(int $aggregateId, ?array $triggerEvent = null)
    {
        if ($triggerEvent && $triggerEvent['projected']){
            echo "The new event with id = ".$aggregateId." is already projected. Nothing to do!\n";
            return;
        }
        echo "Projecting aggregate with id = ".$aggregateId."\n";
        $eventStream = $this->store->getEventStream(aggregateId:$aggregateId);
        $this->manager->beginTransaction();
        try {
            $aggregate = new MyAggregate($eventStream);
            $this->manager->replace(id: $aggregateId, aggregate: $aggregate);
            $this->setProjected($eventStream);
            $this->manager->flush();
            $this->manager->commit();
        }
        catch (EventApplyException $e){
            $this->manager->rollBack();
            $failedEvent = new EventApplyFailedEvent(
                aggregateId: $aggregateId,
                aggregateVersion: $e->getEvent()->getAggregateVersion(),
                correlationId: $e->getEvent()->getId(),
                data: [
                    'exception' => [
                        'class' => $e::class,
                        'code' => $e->getCode(),
                        'trace' => $e->getTrace()
                    ]
                ],
                timestamp: new \DateTimeImmutable(),
                projected: true
            );
            $this->store->add($failedEvent);
        }
        catch (\Exception $e){
            $this->manager->rollBack();
            throw $e;
        }
    }

    public function start(): void
    {
        while (true) {
            $this->store->listen(projector:$this);
        }
    }

    public function projectUnprojected(): void
    {
        echo "Quering for unprojected events ...\n";
        $unprojectedEvents = $this->store->getUnprojectedEvents();
        $aggregateIds = array_unique(array_map(function(Event $event){
            return $event->getAggregateId();
        }, $unprojectedEvents));
        array_walk($aggregateIds, function (int $id){
            $this->project(aggregateId:$id);
        });
    }

    protected function setProjected(array $events): void
    {
        array_walk($events, function(Event $event){
            $event->setProjected();
        });
    }

}