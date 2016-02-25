<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Monitor\Config\ConfigJson as Config;

require __DIR__.'/../vendor/autoload.php';

$paths = ["Model"];
$isDevMode = false;

$config = new Config('Config.json');

$dbParams =
[
    'driver'    => $config->get('db_driver'),
    'user'      => $config->get('db_username'),
    'password'  => $config->get('db_password'),
    'dbname'    => $config->get('db_name'),
];

$DoctrineConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $DoctrineConfig);
