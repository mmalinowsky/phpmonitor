<?php
namespace Monitor\Notification;

use Monitor\Model\Notification;
use Monitor\Model\Trigger;

class NotificationMgr
{
    private $notificationParser;
    private $notificationData;
    private $observers;
    private $repository;
    private $notificationDelay;
    private $notificationLogService;

    public function __construct(
        $notificationData,
        Parser $notificationParser,
        $notificationDelay,
        $notificationLogService,
        $repository
    ) {
    
        $this->notificationData = $notificationData;
        $this->notificationParser = $notificationParser;
        $this->notificationDelay = $notificationDelay;
        $this->repository = $repository;
        $this->notificationLogService = $notificationLogService;
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


    /**
     * Prepare notification
     *
     * @access private
     * @param  Trigger $trigger
     * @param  array   $serverData
     * @return Monitor\Notification\Notification
     */
    public function prepareNotification(Trigger $trigger, array $serverData)
    {
        $notificationId = $trigger->getNotificationId();
        $notification = $this->getNotificationById($notificationId);
        //merge server data and trigger properties so we can use them in fulfilling notification message
        $data = array_merge($serverData, $trigger->toArray());
        $this->parseNotification($notification, $data);
        return $notification;
    }

    /**
     * Check if same type of notification for concret server has been sent already
     *
     * @access private
     * @param  int $triggerId
     * @param  int $serverId
     * @return boolean
     */
    public function hasNotificationDelayExpired($triggerId, $serverId, $msDelay)
    {
        $queryResult = $this->notificationLogService->getLastForTrigger(
            $triggerId,
            $serverId
        );
        if (! $queryResult) {
            return true;
        }
        $timeOfLastFiredUpTrigger = $queryResult[0]['created'];
        $timeDiff = $timeOfLastFiredUpTrigger - time();
        return ($this->notificationDelay * $msDelay + $timeDiff >= 0) ? false : true;
    }
}
