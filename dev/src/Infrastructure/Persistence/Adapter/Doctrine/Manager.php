<?php declare(strict_types=1);

namespace Infrastructure\Persistence\Adapter\Doctrine;

use Application\Persistence\Manager as ApplicationManager;

use Doctrine\ORM\EntityManager as DoctrineManager;
use Closure;

class Manager implements ApplicationManager
{
    protected DoctrineManager $delegate;
    
    protected Closure $delegateFactory;

    public function __construct(callable $delegateFactory)
    {
        $this->delegateFactory = Closure::fromCallable($delegateFactory);
        $this->delegate = ($this->delegateFactory)();
    }

    public function open(): void
    {
        if (! $this->delegate->isOpen()) {
            $this->delegate = ($this->delegateFactory)();
        }
    }

    public function close(): void
    {
        $this->delegate->getConnection()->close();
        $this->delegate->clear();
    }

    public function getRepository(string $className): Repository
    {
        return new Repository($this->delegate->getRepository($className));
    }

    public function replace(mixed $id, object $aggregate):void{
        $existing = $this->delegate->getRepository($aggregate::class)->find($id);
        if ($existing){
            $this->delegate->remove($existing);
            $this->delegate->flush();
        }
        $this->delegate->persist($aggregate);
    }

    public function flush(object $aggregate = null):void {
        $this->delegate->flush($aggregate);
    }
    
    public function beginTransaction():void{
        $this->delegate->beginTransaction();
    }

    public function commit():void{
        $this->delegate->commit();
    }

    public function rollBack():void{
        $this->delegate->rollBack();
    }

    public function persist(object $object):void{
        $this->delegate->persist($object);
    }

}
