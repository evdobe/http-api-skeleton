<?php declare(strict_types=1);

namespace Application\Persistence;

interface Repository
{
    public function find(mixed $id):?object;

    public function findAll():array;

    public function findBy(array $criteria, ?array $orderBy = null): array;
}
