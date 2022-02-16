<?php declare(strict_types=1);

namespace Infrastructure\Persistence\Adapter\Doctrine;

use Application\Persistence\Repository as ApplicationRepository;
use Doctrine\ORM\EntityRepository as DoctrineRepository;

class Repository implements ApplicationRepository
{

    public function __construct(protected DoctrineRepository $delegate)
    {
        
    }

    public function find(mixed $id): ?object
    {
        return $this->delegate->find($id);
    }

    public function findAll(): array
    {
        return $this->delegate->findAll();
    }

    public function findBy(array $criteria, ?array $orderBy = null): array
    {
        return $this->delegate->findBy(criteria: $criteria, orderBy: $orderBy);
    }

}
