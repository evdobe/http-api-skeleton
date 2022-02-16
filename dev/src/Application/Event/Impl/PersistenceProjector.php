<?php

namespace Application\Event\Impl;

use Application\Event\Projector;
use Application\Event\Store;
use Application\Persistence\Manager;
use Domain\Event;
use Domain\MyAggregate;

class PersistenceProjector implements Projector
{
    public function __construct(protected Store $store, protected Manager $manager)
    {
        $this->manager->open();
    }

    public function project(int $aggregateId)
    {
        echo "Projecting aggregate with id = ".$aggregateId."\n";
        $eventStream = $this->store->getEventStream(aggregateId:$aggregateId);
        $aggregate = new MyAggregate($eventStream);
        $this->manager->merge($aggregate);
        $this->setProjected($eventStream);
        $this->manager->flush();
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