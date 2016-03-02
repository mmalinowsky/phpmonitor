<?php
namespace Monitor\Notification\Trigger;

use Monitor\Model\Notification;
use Monitor\Notification\Notifier;
use Monitor\Notification\Trigger\Comparator\Comparator;
use Monitor\Notification\Trigger\Comparator\Strategy\Context as StrategyContext;
use Monitor\Utils\PercentageHelper;
use Monitor\Model\Trigger;
use Monitor\Model\NotificationLog;
use Monitor\Service\NotificationLog as NotificationLogService;
use Doctrine\ORM\EntityRepository;

class TriggerMgr
{
    
    /**
     * @var \Monitor\Model\Trigger
     */
    private $triggers;
    /**
     * @var \Monitor\Notification\Trigger\Comparator\Comparator
     */
    private $comparator;
    /**
     * @var \Monitor\Notification\NotificationMgr
     */
    private $notifier;
    /**
     * @var \Monitor\Utils\PercentageHelper
     */
    private $percentageHelper;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $serviceRepository;
    /**
     * @var \Monitor\Service\NotificationLog
     */
    private $notificationLogService;

    public function __construct(
        Notifier $notifier,
        PercentageHelper $percentageHelper,
        EntityRepository $triggerRepository,
        EntityRepository $serviceRepository,
        NotificationLogService $notificationLogService,
        Comparator $comparator
    ) {
        $this->notifier = $notifier;
        $this->percentageHelper = $percentageHelper;
        $this->triggers = $triggerRepository->findAll();
        $this->serviceRepository = $serviceRepository;
        $this->notificationLogService = $notificationLogService;
        $this->comparator = $comparator;
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
            if ($this->shouldTriggerBeFired($trigger, $serverData, $msDelay)) {
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
     * @param  int     $msDelay
     * @return boolean
     */
    public function shouldTriggerBeFired(Trigger $trigger, array $serverData, $msDelay)
    {
        $this->checkIsComparatorValid();
        if ( ! $this->notifier->hasNotificationDelayExpired(
            $trigger->getId(),
            $serverData['server_id'],
            $msDelay
        )) {
            return false;
        }
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
        if ( ! $this->comparator) {
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
        $notification = $this->notifier->triggerHasBeenFired($trigger, $serverData);
        $log = new NotificationLog;
        $log->setTriggerId($trigger->getId());
        $log->setServerId($serverData['server_id']);
        $log->setMessage($notification->getMessage());
        $log->setCreated(time());
        $this->notificationLogService->save($log);
        return true;
    }
}
