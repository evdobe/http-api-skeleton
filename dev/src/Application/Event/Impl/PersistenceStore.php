<?php

namespace Application\Event\Impl;

use Application\Event\Projector;
use Application\Event\Store;
use Application\Event\StoreListener;
use Application\Persistence\Manager;
use Application\Persistence\Repository;
use Domain\Event;

class PersistenceStore implements Store
{
    protected Repository $repository;

    protected array $projectors = [];

    public function __construct(protected Manager $manager, protected ?StoreListener $listener = null)
    {
        $this->repository = $manager->getRepository(Event::class);
    }

    public function listen(Projector $projector): void
    {
        if (empty($this->listener)){
            throw new \Exception('No listener provided!');
        }
        if (!in_array($projector, $this->projectors)){
            $this->projectors[] = $projector;
        }
        $this->listener->listen($this, $projector);
    }

    public function getUnprojectedEvents():array{
        return $this->repository->findBy(criteria: ['projected' => false]);
    }

    public function getEventStream(int $aggregateId): array
    {
        return $this->repository->findBy(criteria: ['aggregateId' => $aggregateId], orderBy: ['aggregateVersion' => 'ASC', 'timestamp' => 'ASC']);
    }

    public function add(Event $event):void{
        $this->manager->persist($event);
        $this->manager->flush();
    }

    public function notify(int|string $eventId): void
    {
        $event = $this->repository->find($eventId);
        array_walk($this->projectors, function(Projector $projector) use ($event){
            $projector->project($event);
        });
    }
}
