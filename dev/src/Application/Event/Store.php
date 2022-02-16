<?php declare(strict_types=1);

namespace Application\Event;

use Application\Persistence\Manager;

interface Store
{
    public function __construct(Manager $manager, ?StoreListener $listener = null);

    public function listen(Projector $projector):void;

    public function getEventStream(int $aggregateId):array;

    public function getUnprojectedEvents():array;

}
