<?php
namespace Monitor;

require __DIR__.'/../../vendor/autoload.php';

if (isset($_SERVER['REMOTE_ADDR'])) {
    die('Can\'t run monitor directly by web browser, please set crontab.');
}

DEFINE('HOUR_IN_MS', 3600);
DEFINE('DAY_IN_MS', HOUR_IN_MS * 24);

$config = new Config;

$db = new Database\PdoSimple(
    [
        $config->get('hostname'),
        $config->get('username'),
        $config->get('password'),
        $config->get('database'),
        $config->get('dbdriver')
    ]
);

$notificationMgr = new Notification\NotificationMgr(new Notification\Parser);
$triggers = new Notification\Trigger\Triggers($notificationMgr);
$triggers->setComparator(new Notification\Trigger\Comparator\Comparator);
$monitor = new Monitor(
    $config,
    $db,
    new Notification\Facade(
        $config,
        $db,
        $notificationMgr,
        $triggers,
        new Notification\Service\Factory
    )
);

$monitor->setClient(new Client\Http);
$monitor->run();
