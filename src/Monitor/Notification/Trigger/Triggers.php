<?php
namespace Monitor\Notification\Trigger;

use Monitor\Notification\Notification as Notification;
use Monitor\Notification\NotificationMgr;
use Monitor\Database\DatabaseInterface;
use Monitor\Notification\Trigger\Comparator\Comparator;
use Monitor\Notification\Trigger\Comparator\Strategy\Context as StrategyContext;
use Monitor\Utils\PercentageHelper;

class Triggers extends Observable
{
    
    private $triggers;
    private $comparator;
    private $notificationDelay;
    private $db;
    private $notificationData;
    private $notificationMgr;
    private $percentageHelper;

    public function __construct(NotificationMgr $notifcationMgr, PercentageHelper $percentageHelper)
    {
        $this->notificationDelay = 0;
        $this->notificationMgr = $notifcationMgr;
        $this->percentageHelper = $percentageHelper;
    }

    public function setComparator(Comparator $comparator)
    {
        $this->comparator = $comparator;
    }

    public function setNotificationData($data)
    {
        $this->notificationData = $data;
    }

    public function setNotificationDelay($delay)
    {
        $this->notificationDelay = $delay;
    }

    public function setDb(DatabaseInterface &$db)
    {
        $this->db = $db;
    }

    /**
     * Add trigger
     *
     * @access public
     * @param  Trigger $trigger
     * @return
     */
    public function addTrigger(Trigger $trigger)
    {
        $this->triggers[] = $trigger;
    }

    /**
     * Add triggers
     *
     * @access public
     * @param array $triggersData
     */
    public function addTriggersByArray(array $triggersData)
    {
        foreach ($triggersData as $triggerData) {
            $this->addTrigger(new Trigger($triggerData));
        }
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

    /**
     * Prepare notification
     *
     * @access private
     * @param  Trigger $trigger
     * @param  array   $serverData
     * @return Monitor\Notification\Notification
     */
    private function prepareNotification(Trigger $trigger, array $serverData)
    {
        $notificationId = $trigger->getNotificationId();
        $notification = $this->notificationMgr->getNotificationById($notificationId);
        //merge server data and trigger properties so we can use them in fulfilling notification message
        $data = array_merge($serverData, $trigger->toArray());
        $this->notificationMgr->parseNotification($notification, $data);
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
    private function hasNotificationDelayExpired($triggerId, $serverId)
    {
        $timeDiff = $this->db->getLastTriggerTime($triggerId, $serverId) - time();
        return ($this->notificationDelay * HOUR_IN_MS + $timeDiff >= 0) ? false : true;
    }

    /**
     * Check if triggers should be fired up
     *
     * @access public
     * @param  array $serverData
     * @param  array $services
     */
    public function checkTriggers(array $serverData, array $services)
    {
        foreach ($this->triggers as $trigger) {
            if ($this->shouldTriggerBeFired($trigger, $serverData, $services)) {
                $this->fireTrigger($trigger, $serverData);
            }
        }
    }

    /**
     * Check if trigger meet conditions to be fired
     *
     * @access public
     * @param  Trigger $trigger
     * @param  array   $serverData
     * @param  array   $services
     * @return boolean
     */
    public function shouldTriggerBeFired(Trigger $trigger, array $serverData, array $services)
    {
        $this->checkIsComparatorValid();
        $strategy = new StrategyContext($trigger->getType());
        return $strategy->compare($trigger, $serverData, $services, $this->percentageHelper, $this->comparator);
    }

    /**
     * Check comparator validity
     *
     * @access private
     * @throws \Exception if comparator is not valid
     */
    private function checkIsComparatorValid()
    {
        if (! $this->comparator) {
            throw new \Exception('Comparator invalid');
        }
    }

    /**
     * Firing trigger
     *
     * @access private
     * @param  Trigger $trigger
     * @param  array   $serverData
     * @return boolean
     */
    private function fireTrigger(Trigger $trigger, array $serverData)
    {
        if (! $this->hasNotificationDelayExpired($trigger->getId(), $serverData['server_id'])) {
            return false;
        }

        $notification = $this->prepareNotification($trigger, $serverData);
        $this->notifyServices($notification);

        $this->db->logTrigger(
            [
                'id'        => $trigger->getId(),
                'serverId'  => $serverData['server_id'],
                'message'   => $notification->getMessage()
            ]
        );
        return true;
    }
}
