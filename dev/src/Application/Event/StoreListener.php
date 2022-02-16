<?php declare(strict_types=1);

namespace Application\Event;

interface StoreListener
{
    public function __construct();

    public function listen(Projector $projector):void;
}
