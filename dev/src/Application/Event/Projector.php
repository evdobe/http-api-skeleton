<?php declare(strict_types=1);

namespace Application\Event;

use Application\Messaging\MessageBuilder;
use Application\Messaging\Producer;
use Application\Persistence\Manager;
use Domain\Event;

interface Projector
{
    public function __construct(Store $store, Manager $manager);

    public function project(int $aggregateId);
    
    public function start():void;

    public function projectUnprojected():void;
}
