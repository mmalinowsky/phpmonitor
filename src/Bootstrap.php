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
    'driver'    => 'pdo_mysql',
    'user'      => $config->get('username'),
    'password'  => $config->get('password'),
    'dbname'    => $config->get('database'),
];

$DoctrineConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $DoctrineConfig);
