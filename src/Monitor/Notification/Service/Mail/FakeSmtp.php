<?php
namespace Monitor\Notification\Service\Mail;

use Monitor\Model\Notification;
use Monitor\Contract\Notification\Service\ServiceInterface;

class FakeSmtp implements ServiceInterface
{
    
    public function sendNotification(Notification $notification, array $data)
    {
        echo '[SMTP] Send notification'.PHP_EOL;
    }
}
