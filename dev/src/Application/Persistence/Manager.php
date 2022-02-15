<?php declare(strict_types=1);

namespace Application\Persistence;

interface Manager
{
    public function getRepository(string $className):Repository;

}
