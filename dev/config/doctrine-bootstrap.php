<?php declare(strict_types=1);

// bootstrap.php
require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

function GetEntityManager(bool $isDevMode = false){
    $paths = [getcwd()."/src/Domain"];

    // the connection configuration
    $dbParams = [
        'driver'   => 'pdo_pgsql',
        'host' => getenv('DB_HOST'),
        'user' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'dbname' => getenv('DB_NAME'),
        'charset' => 'utf8',
    ];

    $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
    return EntityManager::create($dbParams, $config);
}