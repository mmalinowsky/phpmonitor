<?php
namespace Monitor\Notification\Service;

use Monitor\Notification\Notification as Notification;

interface ServiceInterface
{
    public function sendNotification(Notification $notification, $data);
}
