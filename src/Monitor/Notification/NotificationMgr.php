<?php
namespace Monitor\Notification;

use Monitor\Model\Notification;
use Monitor\Model\Trigger;
use Monitor\Service\NotificationLog as NotificationLogService;
use Doctrine\ORM\EntityRepository;

class NotificationMgr
{

    /**
     * @var \Monitor\Notification\Parser
     */
    private $notificationParser;
    /**
     * @var integer
     */
    private $notificationDelayInHours;
    /**
     * @var \Monitor\Service\NotificationLog
     */
    private $notificationLogService;
    /**
     * Notification repository
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    public function __construct(
        Parser $notificationParser,
        $notificationDelay,
        NotificationLogService $notificationLogService,
        EntityRepository $repository
    ) {
    
        $this->notificationParser = $notificationParser;
        $this->notificationDelayInHours = $notificationDelay;
        $this->notificationLogService = $notificationLogService;
        $this->repository = $repository;
    }

    /**
     * Get Notification by id
     *
     * @param int $id
     * @return \Monitor\Model\Notification $notification
     */
    public function getNotificationById($id)
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
    public function parseNotification(Notification $notification, $data)
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
        if ( ! $queryResult) {
            return true;
        }
        $timeOfLastFiredUpTrigger = $queryResult[0]['created'];
        $timeDiff = $timeOfLastFiredUpTrigger - time();
        return ($this->notificationDelayInHours * $msDelay + $timeDiff >= 0) ? false : true;
    }
}
