<?php
namespace Monitor;

require __DIR__.'/bootstrap.php';

if (isset($_SERVER['REMOTE_ADDR'])) {
    throw new \Exception('Can\'t run monitor directly by web browser, please set crontab.');
}

$formatFactory = new Format\Factory;
$notificationMgr = new Notification\NotificationMgr(
    new Notification\Parser,
    $entityManager->getRepository('Monitor\Model\Notification')
);
$triggerMgr = new Notification\Trigger\TriggerMgr(
    $notificationMgr,
    new Utils\PercentageHelper,
    $entityManager->getRepository('Monitor\Model\Service'),
    $entityManager->getRepository('Monitor\Model\Trigger')
);
$triggerMgr->setComparator(new Notification\Trigger\Comparator\Comparator);
$format = $formatFactory->build($config->get('format'));
$monitor = new Monitor(
    $config,
    $db,
    new Notification\Facade(
        $config,
        $db,
        $notificationMgr,
        $triggerMgr,
        new Notification\Service\Factory,
        $entityManager->getRepository('Monitor\Model\Service')
    ),
    $format,
    $entityManager
);
$monitor->setClient(new Client\Http\Http);
$monitor->run();
