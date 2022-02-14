<?php

declare(strict_types=1);

use Infrastructure\Execution\Adapter\Swoole\Timer;

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            // Fully\Qualified\ClassOrInterfaceName::class => Fully\Qualified\ClassName::class,
            \Application\Http\Application::class => \Infrastructure\Http\Adapter\MezzioSwoole\Application::class,
            \Application\Http\Server::class => \Infrastructure\Http\Adapter\MezzioSwoole\Server::class,
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
        ],
    ],
];
