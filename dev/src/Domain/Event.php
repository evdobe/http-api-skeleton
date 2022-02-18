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
#[DiscriminatorMap([
    'Collaborator:MyAggregateCreated' => CreatedEvent::class,
    'Collaborator:MyAggregateActivated' => ActivatedEvent::class,
    'HttpApi:EventApplyFailed' => EventApplyFailedEvent::class
])]
abstract class Event
{

    protected readonly string $name;

    public function __construct(

        

        #[Column(type:"integer", insertable: true, updatable: false)]
        protected readonly int $aggregateId,

        #[Column(type:"integer", insertable: true, updatable: false)]
        protected readonly int $aggregateVersion,

        #[Column(type:"json", insertable: true, updatable: false)]
        protected readonly array $data,

        #[Column(type:"datetime_immutable", insertable: true, updatable: false)]
        protected readonly DateTimeImmutable $timestamp,

        #[Column(type:"boolean", options:["default" => false], insertable: true, updatable: true)]
        protected bool $projected = false,

        #[Column(type:"boolean", options:["default" => false], insertable: true, updatable: false)]
        protected readonly bool $dispatched = false,

        #[Column(type:"integer", insertable: false, updatable: false)]
        #[Id]
        #[GeneratedValue(strategy:'IDENTITY')]
        protected ?int $id = null,

        #[Column(type:"datetime_immutable", nullable:true, insertable: true, updatable: false)]
        protected readonly ?DateTimeImmutable $dispatchedAt = null,

        #[Column(type:"integer", nullable:true, insertable: true, updatable: false)]
        protected readonly ?int $correlationId = null,

        #[Column(type:"string", nullable:true , insertable: true, updatable: false)]
        protected readonly ?string $channel = null,

        #[Column(type:"datetime_immutable", nullable:true, insertable: true, updatable: false)]
        protected readonly ?DateTimeImmutable $receivedAt = null,

    ){
        $this->name = match($this::class){
            CreatedEvent::class => 'Collaborator:MyAggregateCreated',
            ActivatedEvent::class => 'Collaborator:MyAggregateActivated',
            EventApplyFailedEvent::class => 'HttpApi:EventApplyFailed'
        };
    }

    public function getId():?int{
        return $this->id;
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

    public function getTimestamp():DateTimeImmutable{
        return $this->timestamp;
    }

    public function isProjected():bool{
        return $this->projected;
    }
}
