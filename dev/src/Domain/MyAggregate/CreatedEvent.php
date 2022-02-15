<?php

namespace Domain\MyAggregate;

use Doctrine\ORM\Mapping\Entity;
use Domain\Event\Event;

#[Entity()]
class CreatedEvent extends Event
{

}
