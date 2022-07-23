<?php

namespace Application\Event\Impl;

use Application\Event\Projector;
use Application\Event\Store;
use Application\Persistence\Manager;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Domain\Event;
use Domain\EventApplyFailedEvent;
use Domain\Exception\EventApplyException;
use Domain\MyAggregate;
use PDOException;

class PersistenceProjector implements Projector
{
    public function __construct(protected Store $store, protected Manager $manager)
    {
        $this->manager->open();
    }

    public function project(Event $event)
    {
        $aggregateId = $event->getAggregateId();
        if ($event->isProjected()){
            echo "The new event with id = ".$aggregateId." is already projected. Nothing to do!\n";
            return;
        }
        echo "Projecting aggregate with id = ".$aggregateId."... ";
        $eventStream = $this->store->getEventStream(aggregateId:$aggregateId);
        $this->manager->beginTransaction();
        try {
            $aggregate = new MyAggregate($eventStream);
            $this->manager->replace(id: $aggregateId, aggregate: $aggregate);
            $this->setProjected($eventStream);
            $this->manager->flush();
            $this->manager->commit();
            echo "Done.\n";
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
            echo "Error! EventApplyFailedEvent occured.\n";
        }
        catch (UniqueConstraintViolationException $e){
            $this->manager->rollBack();
            $this->manager->open();
            echo "Aborting on UniqueConstraintViolationException.\n";
            
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
        array_walk($unprojectedEvents, function (Event $event){
            $this->project($event);
        });
    }

    protected function setProjected(array $events): void
    {
        array_walk($events, function(Event $event){
            $event->setProjected();
        });
    }

}