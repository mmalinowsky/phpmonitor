<?php
namespace Monitor\Notification\Service;

use Monitor\Model\Notification;

interface ServiceInterface
{
    public function sendNotification(Notification $notification, $data);
}
