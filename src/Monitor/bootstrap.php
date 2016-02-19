<?php

require __DIR__.'/../../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = ["Model"];
$isDevMode = false;

$config = new Monitor\Config\ConfigJson;
$config->loadFromFile('Config.json');

$dbParams =
[
    'driver'    => 'pdo_mysql',
    'user'      => $config->get('username'),
    'password'  => $config->get('password'),
    'dbname'    => $config->get('database'),
];

$ormConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $ormConfig);
