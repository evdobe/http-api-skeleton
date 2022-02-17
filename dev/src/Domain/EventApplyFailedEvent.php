<?php declare(strict_types=1);

namespace Domain;

use Doctrine\ORM\Mapping\Entity;

#[Entity()]
class EventApplyFailedEvent extends Event
{

}
