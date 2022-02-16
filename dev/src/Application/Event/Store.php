<?php declare(strict_types=1);

namespace Application\Event;

use Application\Persistence\Manager;

interface Store
{
    public function __construct(StoreListener $listener, Manager $manager);

    public function listen(Projector $projector):void;

    public function getEventStream(int $aggregateId):array;

    public function getUnprojectedEvents():array;

}
