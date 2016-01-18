<?php
namespace Monitor\Notification;

class NotificationMgr
{
    private $notifications = [];
    private $notificationParser;
    private $notificationData;
    private $observers;
    
    public function __construct(Parser $notificationParser)
    {
        $this->notificationParser = $notificationParser;
    }

    public function setNotificationData($data)
    {
        $this->notificationData = $data;
    }

    public function addNotification(Notification $notification)
    {
        $this->notifications[] = $notification;
    }

    public function getNotificationById($id)
    {
        foreach ($this->notifications as $notification) {
            if ($notification->getId() == $id) {
                return $notification;
            }
        }
        return null;
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
