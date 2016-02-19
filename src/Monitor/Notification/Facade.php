<?php
namespace Monitor\Notification;

use Monitor\Notification\Trigger\TriggerMgr;
use Monitor\Config\ConfigInterface;
use Monitor\Notification\Service\Factory as ServiceFactory;

class Facade
{
    private $notificationMgr;
    private $triggerMgr;

    public function __construct(
        ConfigInterface $config,
        NotificationMgr $notificationMgr,
        TriggerMgr $triggerMgr,
        ServiceFactory $serviceFactory
    ) {
        $this->triggerMgr = $triggerMgr;
        $this->notificationMgr = $notificationMgr;
        $this->notificationMgr->setNotificationData($config->get('notification')['data']);
        $this->triggerMgr->setNotificationDelay($config->get('notification_delay_in_hours'));
        $this->addObservers($config->get('notification')['services'], $serviceFactory);
    }

    private function addObservers($observers, ServiceFactory $serviceFactory)
    {
        foreach ($observers as $observer) {
            try {
                $service = $serviceFactory->getService($observer);
                $this->triggerMgr->addObserver($service);
            } catch (\Exception $e) {
                $this->triggerMgr->popObserver();
            }
        }
    }

    /**
     * Check triggers for concrete server
     *
     * @param array $serverData
     */
    public function checkTriggers(array $serverData, $msInHour)
    {
        $this->triggerMgr->checkTriggers($serverData, $msInHour);
    }
}
