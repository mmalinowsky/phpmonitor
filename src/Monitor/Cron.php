<?php
namespace Monitor;

require __DIR__.'/bootstrap.php';

if (isset($_SERVER['REMOTE_ADDR'])) {
    throw new \Exception('Can\'t run monitor directly by web browser, please set crontab.');
}

$db = new Database\PdoSimple(
    [
        $config->get('hostname'),
        $config->get('username'),
        $config->get('password'),
        $config->get('database'),
        $config->get('dbdriver')
    ]
);
$formatFactory = new Format\Factory;
$notificationMgr = new Notification\NotificationMgr(new Notification\Parser, $entityManager->getRepository('Monitor\Model\Notification'));
$triggers = new Notification\Trigger\Triggers($notificationMgr, new Utils\PercentageHelper, $entityManager->getRepository('Monitor\Model\Service'));
$triggers->setComparator(new Notification\Trigger\Comparator\Comparator);
$triggers->setRepository($entityManager->getRepository('Monitor\Model\Trigger'));
$format = $formatFactory->build($config->get('format'));
$monitor = new Monitor(
    $config,
    $db,
    new Notification\Facade(
        $config,
        $db,
        $notificationMgr,
        $triggers,
        new Notification\Service\Factory,
        $entityManager->getRepository('Monitor\Model\Service')
    ),
    $format,
    $entityManager
);
$monitor->setClient(new Client\Http\Http);
$monitor->run();
