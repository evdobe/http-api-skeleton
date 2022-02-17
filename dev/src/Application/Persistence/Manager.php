<?php declare(strict_types=1);

namespace Application\Persistence;

interface Manager
{
    public function open():void;

    public function close():void;
    
    public function getRepository(string $className):Repository;

    public function replace(mixed $id, object $aggregate):void;

    public function flush(object $aggregate = null):void;

    public function beginTransaction():void;

    public function commit():void;

    public function rollBack():void;

}
