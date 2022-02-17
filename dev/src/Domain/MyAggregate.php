<?php declare(strict_types=1);

namespace Domain;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Id;
use JsonSerializable;

#[Entity()]
#[Table(name:"my_aggregate")]
class MyAggregate implements JsonSerializable
{

    #[Column(type:"integer", insertable: true, updatable: false)]
    #[Id]
    private readonly int $id;

    #[Column(type:"integer")]
    private int $version;

    #[Column(type:"json")]
    private array $data;

    #[Embedded(class:Status::class)]
    private Status $status;

    public function __construct(array $eventStream){
        array_walk($eventStream, function(Event $event){
            $this->apply($event);
        });
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'version' => $this->version,
            'data' => $this->data,
            'status' => $this->status->jsonSerialize()
        ];
    }

    public function apply(Event $event){
        return match($event::class){
            CreatedEvent::class => $this->applyCreated($event),
            ActivatedEvent::class => $this->applyActivated($event),
            EventApplyFailedEvent::class => function(){}
        };
    }

    protected function applyCreated(CreatedEvent $event){
        if (isset($this->id) && $this->id !=  $event->getAggregateId()){
            throw new \Exception(
                message: '!!!!ERROR!!! Trying to apply event with id ='.$event->getId().' to wrong aggregate!!!!! This id = '.$this->id.' while event aggregate_id = '.$event->getAggregateId()
            );
        }
        if (!isset($this->id)){
            $this->id = $event->getAggregateId();
        }
        $this->version = $event->getAggregateVersion();
        $this->data = $event->getData();
        $this->status = new Status(code: StatusCode::INACTIVE, by: null, at: $event->getTimestamp());
    }

    protected function applyActivated(ActivatedEvent $event){
        if ($event->getAggregateVersion() != $this->version+1){
            throw new Exception\EventApplyException(
                message: 'Event stream is corrupted! Could not applyActivated: Current agregate version: '.$this->version.', event version: '.$event->getAggregateVersion(),
                code: 1,
                event: $event
            );
        }
        $this->version = $event->getAggregateVersion();
        $this->status = new Status(code: StatusCode::ACTIVE, by: $event->getData()['by'], at: $event->getTimestamp());
    }

}
