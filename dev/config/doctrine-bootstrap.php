<?php declare(strict_types=1);

// bootstrap.php
require_once __DIR__."/../vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$getDoctrineEntityManager = function(){
    $isDevMode = (getenv('APPLICATION_ENVIRONMENT') == 'dev');
    echo 'isDevMode:'; var_dump($isDevMode);
    $paths = [__DIR__."/../src/Domain"];

    // the connection configuration
    $dbParams = [
        'driver'   => 'pdo_pgsql',
        'host' => getenv('DB_HOST'),
        'user' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'dbname' => getenv('DB_NAME'),
        'charset' => 'utf8',
    ];

    $config = Setup::createAttributeMetadataConfiguration($paths, $isDevMode);

    $namingStrategy = new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy(CASE_LOWER);
    $config->setNamingStrategy($namingStrategy);
    
    return EntityManager::create($dbParams, $config);
};