<?php
namespace Monitor;

require __DIR__.'/bootstrap.php';

$formatFactory = new Format\Factory;
$notificationMgr = new Notification\NotificationMgr(
    new Notification\Parser,
    $entityManager->getRepository('Monitor\Model\Notification')
);
$triggerMgr = new Notification\Trigger\TriggerMgr(
    $notificationMgr,
    new Utils\PercentageHelper,
    $entityManager
);
$triggerMgr->setComparator(new Notification\Trigger\Comparator\Comparator);
$format = $formatFactory->build($config->get('format'));
$monitor = new Monitor(
    $config,
    new Notification\Facade(
        $config,
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
