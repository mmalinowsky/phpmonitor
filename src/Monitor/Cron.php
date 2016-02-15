<?php
namespace Monitor;

require __DIR__.'/../../vendor/autoload.php';

if (isset($_SERVER['REMOTE_ADDR'])) {
    throw new \Exception('Can\'t run monitor directly by web browser, please set crontab.');
}

$config = new Config\ConfigJson;
$config->loadFromFile('Config.json');

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
$notificationMgr = new Notification\NotificationMgr(new Notification\Parser);
$triggers = new Notification\Trigger\Triggers($notificationMgr, new Utils\PercentageHelper);
$triggers->setComparator(new Notification\Trigger\Comparator\Comparator);
$format = $formatFactory->build($config->get('format'));
$monitor = new Monitor(
    $config,
    $db,
    new Notification\Facade(
        $config,
        $db,
        $notificationMgr,
        $triggers,
        new Notification\Service\Factory
    ),
    $format
);

$monitor->setClient(new Client\Http\Http);
$monitor->run();
