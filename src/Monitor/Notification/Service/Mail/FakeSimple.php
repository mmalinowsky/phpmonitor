<?php
namespace Monitor\Notification\Service\Mail;

use Monitor\Model\Notification;
use Monitor\Notification\Service\ServiceInterface as ServiceInterface;

class FakeSimple implements ServiceInterface
{

    public function sendNotification(Notification $notification, $data)
    {
        echo '[Mail] Send Notification - ';
        echo $notification->getMessage().PHP_EOL;
    }
}
