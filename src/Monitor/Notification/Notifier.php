<?php
namespace Monitor\Notification;

use Monitor\Model\Notification;
use Monitor\Model\Trigger;
use Monitor\Service\NotificationLog as NotificationLogService;
use Doctrine\ORM\EntityRepository;

class Notifier extends Observable
{

    /**
     * @var \Monitor\Notification\Parser
     */
    private $notificationParser;
    /**
     * @var \Monitor\Service\NotificationLog
     */
    private $notificationLogService;
    /**
     * Notification repository
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;
    /**
     * @var array
     */
    private $notificationData;

    public function __construct(
        Parser $notificationParser,
        NotificationLogService $notificationLogService,
        EntityRepository $repository
    ) {
    
        $this->notificationParser = $notificationParser;
        $this->notificationLogService = $notificationLogService;
        $this->repository = $repository;
    }

    /**
     * Add notification data, we will use them in notification services
     *
     * @param array $data
     */
    public function setNotificationData(array $data)
    {
        $this->notificationData = $data;
    }
    /**
     * Get Notification by id
     *
     * @param int $id
     * @return \Monitor\Model\Notification $notification
     */
    private function getNotificationById($id)
    {
        $notification = $this->repository->find($id);
        return $notification;
    }

    /**
     * Parse notification message
     *
     * @param \Monitor\Model\Notification $notification
     * @param array $data
     */
    private function parseNotification(Notification $notification, $data)
    {
        $this->notificationParser->parse($notification, $data);
    }

    /**
     * Prepare notification
     *
     * @access private
     * @param  Trigger $trigger
     * @param  array   $serverData
     * @return \Monitor\Notification\Notification
     */
    private function prepareNotification(Trigger $trigger, array $serverData)
    {
        $notificationId = $trigger->getNotificationId();
        $notification = $this->getNotificationById($notificationId);
        //merge server data and trigger properties so we can use them in fulfilling notification message
        $data = array_merge($serverData, $trigger->toArray());
        $this->parseNotification($notification, $data);
        return $notification;
    }

    /**
     *
     *
     * @param  Trigger $trigger
     * @param  array   $serverData
     * @return \Monitor\Notification\Notification
     */
    public function triggerHasBeenFired(Trigger $trigger, array $serverData)
    {
        $notification = $this->prepareNotification($trigger, $serverData);
        $this->notifyServices($notification);
        return $notification;
    }

    /**
     * Send notification to notification service
     *
     * @access public
     * @param  Notification $notification
     * @return
     */
    public function notifyServices(Notification $notification)
    {
        foreach ($this->observers as $observer) {
            $observer->sendNotification($notification, $this->notificationData);
        }
    }
}
