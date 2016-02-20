<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require __DIR__.'/../../vendor/autoload.php';

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

$DoctrineConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $DoctrineConfig);
