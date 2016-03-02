<?php

use Monitor\Notification\Trigger\Comparator\Comparator;
use Monitor\Notification\Service\Factory as ServiceFactory;
use Monitor\Notification\Trigger\TriggerMgr;
use Monitor\Notification\Facade as NotificationFacade;
use Monitor\Notification\Notifier;
use Monitor\Notification\Parser as NotificationParser;
use Monitor\Service\NotificationLog as NotificationLogService;
use Monitor\Service\ServerHistory as ServerHistoryService;
use Monitor\Format\Factory as FormatFactory;
use Monitor\Utils\PercentageHelper;
use Monitor\Client\Http\Http as Http;
use Monitor\Monitor as Monitor;
use Monitor\Utils\ArrayHelper;

require __DIR__.'/Bootstrap.php';

$formatFactory = new FormatFactory;
$format = $formatFactory->build($config->get('format'));

$notificationLogService = new NotificationLogService(
    $entityManager,
    $config->get('notification_delay_in_hours')
);

$notifier = new Notifier(
    new NotificationParser,
    $notificationLogService,
    $entityManager->getRepository('Monitor\Model\Notification')
);
$notifier->setNotificationData($config->get('notification')['data']);

$triggerMgr = new TriggerMgr(
    $notifier,
    new PercentageHelper,
    $entityManager->getRepository('Monitor\Model\Trigger'),
    $entityManager->getRepository('Monitor\Model\Service'),
    $notificationLogService,
    new Comparator
);

$notificationFacade = new NotificationFacade(
        $config,
        $triggerMgr,
        new ServiceFactory,
        $notifier
);

$monitor = new Monitor(
    $config,
    $notificationFacade,
    $format,
    $entityManager->getRepository('Monitor\Model\Server')->findAll(),
    new ServerHistoryService($entityManager),
    new ArrayHelper
);
$monitor->setClient(new Http);
$monitor->run();
