#!/usr/bin/env php
<?php declare(strict_types=1);

use Application\Event\Projector;
use Application\Event\Store;
use Application\Http\Application;
use Application\Http\Server;
use Application\Execution\Process;
use Application\Execution\Timer;
use Application\Persistence\Manager;
use DI\Container;

// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/**
 * Self-called anonymous function that creates its own scope and keeps the global namespace clean.
 */
(function () {
    /** @var \DI\Container $container */
    $container = require 'config/container.php';

    /** @var Application $app */
    $app = $container->get(Application::class);

    // Execute programmatic/declarative middleware pipeline and routing
    // configuration statements
    (require 'config/pipeline.php')($app);
    (require 'config/routes.php')($app);

    $server = $container->get(Server::class);

    $process = $container->make(Process::class, ["callback" => function($process) use ($container){
        echo "Starting projector process...\n";
        $projector = $container->make(Projector::class, [
            'store' => $container->make(Store::class),
            'manager' => $container->make(Manager::class)
        ]);
        $projector->start();
        sleep(1);
    }]);

    $server->addProcess($process);

    $timer = $container->make(Timer::class);

    $server->on("start", function(Server $server) use ($timer, $container){
        $projector = $container->make(Projector::class, [
            'store' => $container->make(Store::class),
            'manager' => $container->make(Manager::class)
        ]);
        $projector->projectUnprojected();
        $timer->tick(2*60*1000, function() use ($projector){
            echo "Priodically checking for unprojected events...\n";
            $projector->projectUnprojected();
            sleep(1);
        });
    });
    
    $app->run();
})();