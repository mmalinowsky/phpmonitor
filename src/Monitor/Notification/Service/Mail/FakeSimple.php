<?php
namespace Monitor\Notification\Service\Mail;

use Monitor\Model\Notification;
use Monitor\Contract\Notification\Service\ServiceInterface;

class FakeSimple implements ServiceInterface
{

    public function sendNotification(Notification $notification, array $data)
    {
        echo '[Mail] Send Notification - ';
        echo $notification->getMessage().PHP_EOL;
    }
}
