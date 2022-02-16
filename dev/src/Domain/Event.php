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
        private array $data,

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
