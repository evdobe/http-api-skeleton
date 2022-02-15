<?php declare(strict_types=1);

namespace Domain\Event;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Domain\MyAggregate\CreatedEvent;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[Entity()]
#[InheritanceType("SINGLE_TABLE")]
#[DiscriminatorColumn(name:"name", type:"string")]
#[DiscriminatorMap(['HttpApiSkeleton:MyAggregateCreated' => CreatedEvent::class])]
abstract class Event
{
    public function __construct(

        #[Column(type:"integer")]
        #[Id]
        #[GeneratedValue(strategy:'IDENTITY')]
        private int $id,

        private string $name,

        #[Column(type:"string")]
        private string $channel,

        #[Column(type:"integer", nullable:true)]
        private ?int $correlationId = null,

        #[Column(type:"integer")]
        private int $aggregateId,

        #[Column(type:"integer")]
        private int $aggregateVersion,

        #[Column(type:"json")]
        private int $data,

        #[Column(type:"datetime")]
        private DateTimeImmutable $timestamp,

        #[Column(type:"boolean", options:["default" => false])]
        private bool $dispatched = false,

        #[Column(type:"datetime", nullable:true)]
        private ?DateTimeImmutable $dispatchedAt = null,

        #[Column(type:"datetime", nullable:true)]
        private ?DateTimeImmutable $receivedAt = null,
        
        #[Column(type:"boolean", options:["default" => false])]
        private bool $projected = false,

    ){
        
    }
}
