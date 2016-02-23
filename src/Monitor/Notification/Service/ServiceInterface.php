<?php
namespace Monitor\Notification\Service;

use Monitor\Model\Notification;

interface ServiceInterface
{

    /**
     * Send notification
     *
     * @param \Monitor\Model\Notification $notification
     * @param array $data
     */
    public function sendNotification(Notification $notification, array $data);
}
