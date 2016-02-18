<?php
namespace Monitor\Notification;

use Monitor\Model\Notification;

class NotificationMgr
{
    private $notifications = [];
    private $notificationParser;
    private $notificationData;
    private $observers;
    private $repository;
    
    public function __construct(Parser $notificationParser, $repository)
    {
        $this->notificationParser = $notificationParser;
        $this->repository = $repository;
    }

    public function setNotificationData($data)
    {
        $this->notificationData = $data;
    }

    public function getNotificationById($id)
    {
        $notification = $this->repository->find($id);
        return $notification;
    }

    public function parseNotification(Notification $notification, $data)
    {
        $this->notificationParser->parse($notification, $data);
    }

    /**
     * Send notification to notification service
     *
     * @access public
     * @param  Notification $notification
     * @return
     */
    public function notifyAllServices(Notification $notification)
    {
        foreach ($this->observers as $observer) {
            $observer->sendNotification($notification, $this->notificationData);
        }
    }
}
