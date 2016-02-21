<?php
namespace Monitor\Notification\Trigger;

use Monitor\Model\Notification;
use Monitor\Notification\NotificationMgr;
use Monitor\Notification\Trigger\Comparator\Comparator;
use Monitor\Notification\Trigger\Comparator\Strategy\Context as StrategyContext;
use Monitor\Utils\PercentageHelper;
use Monitor\Model\Trigger;
use Monitor\Model\NotificationLog;
use Monitor\Service\NotificationLog as NotificationLogService;

class TriggerMgr extends Observable
{
    
    private $triggers;
    private $comparator;
    private $notificationData;
    private $notificationMgr;
    private $percentageHelper;
    private $serviceRepository;
    private $notificationLogService;

    public function __construct(
        NotificationMgr $notifcationMgr,
        PercentageHelper $percentageHelper,
        $triggerRepository,
        $serviceRepository,
        NotificationLogService $notificationLogService,
        Comparator $comparator
    ) {
        $this->notificationMgr = $notifcationMgr;
        $this->percentageHelper = $percentageHelper;
        $this->triggers = $triggerRepository->findAll();
        $this->serviceRepository = $serviceRepository;
        $this->notificationLogService = $notificationLogService;
        $this->comparator = $comparator;
    }

    public function setNotificationData($data)
    {
        $this->notificationData = $data;
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
     * Check if triggers should be fired up
     *
     * @access public
     * @param  array $serverData
     */
    public function checkTriggers(array $serverData, $msDelay)
    {
        foreach ($this->triggers as $trigger) {
            if ($this->shouldTriggerBeFired($trigger, $serverData)) {
                $this->fireTrigger($trigger, $serverData, $msDelay);
            }
        }
    }

    /**
     * Check if trigger meet conditions to be fired
     *
     * @access public
     * @param  Trigger $trigger
     * @param  array   $serverData
     * @return boolean
     */
    public function shouldTriggerBeFired(Trigger $trigger, array $serverData)
    {
        $this->checkIsComparatorValid();
        $strategy = new StrategyContext($trigger->getType());
        return $strategy->compare(
            $trigger,
            $serverData,
            $this->serviceRepository,
            $this->percentageHelper,
            $this->comparator
        );
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
    private function fireTrigger(Trigger $trigger, array $serverData, $msDelay)
    {
        if (! $this->notificationMgr->hasNotificationDelayExpired(
            $trigger->getId(),
            $serverData['server_id'],
            $msDelay
        )) {
            return false;
        }

        $notification = $this->notificationMgr->prepareNotification($trigger, $serverData);
        $this->notifyServices($notification);
        $log = new NotificationLog;
        $log->setTriggerId($trigger->getId());
        $log->setServerId($serverData['server_id']);
        $log->setMessage($notification->getMessage());
        $log->setCreated(time());
        $this->notificationLogService->save($log);
        return true;
    }
}
