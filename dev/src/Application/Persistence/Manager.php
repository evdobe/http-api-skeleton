<?php declare(strict_types=1);

namespace Application\Persistence;

interface Manager
{
    public function open():void;

    public function close():void;
    
    public function getRepository(string $className):Repository;

}
