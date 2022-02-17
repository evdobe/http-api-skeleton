<?php declare(strict_types=1);

namespace Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;

#[Entity()]
#[InheritanceType("SINGLE_TABLE")]
#[DiscriminatorColumn(name:"name", type:"string")]
#[DiscriminatorMap(['Collaborator:MyAggregateCreated' => CreatedEvent::class])]
abstract class Event
{
    public function __construct(

        #[Column(type:"integer", insertable: true, updatable: false)]
        #[Id]
        #[GeneratedValue(strategy:'IDENTITY')]
        private readonly int $id,

        private readonly string $name,

        #[Column(type:"string", insertable: true, updatable: false)]
        private readonly string $channel,

        #[Column(type:"integer", insertable: true, updatable: false)]
        private readonly int $aggregateId,

        #[Column(type:"integer", insertable: true, updatable: false)]
        private readonly int $aggregateVersion,

        #[Column(type:"json", insertable: true, updatable: false)]
        private readonly array $data,

        #[Column(type:"datetime_immutable", insertable: true, updatable: false)]
        private readonly DateTimeImmutable $timestamp,

        #[Column(type:"boolean", options:["default" => false], insertable: true, updatable: true)]
        private bool $projected = false,

        #[Column(type:"boolean", options:["default" => false], insertable: true, updatable: false)]
        private readonly bool $dispatched = false,

        #[Column(type:"datetime_immutable", nullable:true, insertable: true, updatable: false)]
        private readonly ?DateTimeImmutable $dispatchedAt = null,

        #[Column(type:"integer", nullable:true, insertable: true, updatable: false)]
        private readonly ?int $correlationId = null,

        #[Column(type:"datetime_immutable", nullable:true, insertable: true, updatable: false)]
        private readonly ?DateTimeImmutable $receivedAt = null,

    ){
        
    }

    public function getName():string{
        return $this->name;
    }

    public function getAggregateId():int{
        return $this->aggregateId;
    }

    public function getAggregateVersion():int{
        return $this->aggregateVersion;
    }

    public function getData():array{
        return $this->data;
    }

    public function setProjected():void{
        $this->projected = true;
    }
}
