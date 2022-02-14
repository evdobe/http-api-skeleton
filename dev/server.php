#!/usr/bin/env php
<?php declare(strict_types=1);

use Application\Http\Application;
use Application\Http\Server;
use Application\Execution\Process;
use Application\Execution\Timer;

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
    /** @var \Psr\Container\ContainerInterface $container */
    $container = require 'config/container.php';

    /** @var Application $app */
    $app = $container->get(Application::class);

    // Execute programmatic/declarative middleware pipeline and routing
    // configuration statements
    (require 'config/pipeline.php')($app);
    (require 'config/routes.php')($app);

    $server = $container->get(Server::class);

    $process = $container->make(Process::class, ["callback" => function($process){
        echo "Dummy process is running...\n";
        sleep(5);
    }]);

    $server->addProcess($process);

    $timer = $container->make(Timer::class);

    $server->on("start", function(Server $server) use ($timer){
        $timer->tick(10*1000, function(){
            echo "Dummy timer is ticking...\n";
            sleep(1);
        });
    });
    
    $app->run();
})();