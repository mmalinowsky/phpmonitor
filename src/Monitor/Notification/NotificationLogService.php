<?php
namespace Monitor\Notification;

use Monitor\Model\NotificationLog;

class NotificationLogService
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function save(NotificationLog $notificationLog)
    {
        $this->em->persist($notificationLog);
        $this->em->flush();
    }
}