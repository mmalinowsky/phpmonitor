<?php
namespace Monitor\Notification\Service\Mail;

use Monitor\Model\Notification;
use \Monitor\Notification\Service\ServiceInterface as ServiceInterface;

class FakeSmtp implements ServiceInterface
{
    
    public function sendNotification(Notification $notification, $data)
    {
        echo '[SMTP] Send notification'.PHP_EOL;
    }
}
