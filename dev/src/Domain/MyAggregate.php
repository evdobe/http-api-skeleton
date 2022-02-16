<?php declare(strict_types=1);

namespace Domain;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;

#[Entity()]
#[Table(name:"my_aggregate")]
class MyAggregate
{

    #[Column(type:"integer")]
    #[Id]
    private int $id;

    #[Column(type:"integer")]
    private int $version;

    #[Column(type:"json")]
    private array $data;

    public function __construct(array $eventStream){
        array_walk($eventStream, function(Event $event){
            $this->apply($event);
        });
    }

    public function apply(Event $event){
        match($event::class){
            CreatedEvent::class => function() use ($event){
                $this->id = $event->getAggregateId();
                $this->version = $event->getAggregateVersion();
                $this->data = $event->getData();
            }
        };
    }

}
