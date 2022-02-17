<?php declare(strict_types=1);

namespace Domain\Exception;

use Domain\Event;

class EventApplyException extends \Exception
{

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null, protected ?Event $event = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getEvent():?Event{
        return $this->event;
    }

}
