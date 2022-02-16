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

    public function __construct(protected StoreListener $listener, Manager $manager)
    {
        $this->repository = $manager->getRepository(Event::class);
    }

    public function listen(Projector $projector): void
    {
        $this->listener->listen($projector);
    }

    public function getUnprojectedEvents():array{
        return $this->repository->findBy(criteria: ['projected' => false]);
    }

    public function getEventStream(int $aggregateId): array
    {
        return $this->repository->findBy(criteria: ['aggregateId' => $aggregateId], orderBy: ['aggregateVersion' => 'ASC']);
    }

}
