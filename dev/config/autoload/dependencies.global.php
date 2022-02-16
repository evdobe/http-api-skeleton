<?php

declare(strict_types=1);

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            // Fully\Qualified\ClassOrInterfaceName::class => Fully\Qualified\ClassName::class,
            \Application\Http\Application::class => \Infrastructure\Http\Adapter\Mezzio\Application::class,
            \Application\Http\Server::class => \Infrastructure\Http\Adapter\Swoole\Server::class,
            \Application\Persistence\Manager::class => \Infrastructure\Persistence\Adapter\Doctrine\Manager::class,
            \Application\Event\Store::class => \Application\Event\Impl\PersistenceStore::class,
            \Application\Event\StoreListener::class => Infrastructure\Event\Adapter\Postgres\StoreListener::class,
            \Application\Event\Projector::class => \Application\Event\Impl\PersistenceProjector::class,
        ],
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
            // Fully\Qualified\InterfaceName::class => Fully\Qualified\ClassName::class,
            \Application\Execution\Process::class => \Infrastructure\Execution\Adapter\Swoole\Process::class,
            \Application\Execution\Timer::class => \Infrastructure\Execution\Adapter\Swoole\Timer::class
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            // Fully\Qualified\ClassName::class => Fully\Qualified\FactoryName::class,
            \Infrastructure\Persistence\Adapter\Doctrine\Manager::class => function(){
                require __DIR__.'/../doctrine-bootstrap.php';
                return new \Infrastructure\Persistence\Adapter\Doctrine\Manager($getDoctrineEntityManager);
            },
        ],
    ],
];
