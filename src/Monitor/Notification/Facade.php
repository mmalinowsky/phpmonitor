<?php
namespace Monitor\Notification;

use Monitor\Notification\Trigger\Triggers;
use Monitor\Database\DatabaseInterface;
use Monitor\Config\ConfigInterface;
use Monitor\Notification\Service\Factory as ServiceFactory;

class Facade
{
    private $notificationMgr;
    private $triggers;
    private $services;
    private $db;

    public function __construct(
        ConfigInterface $config,
        DatabaseInterface &$db,
        NotificationMgr $notificationMgr,
        Triggers $triggers,
        ServiceFactory $serviceFactory
    ) {
        $this->db = $db;
        $this->triggers = $triggers;
        $this->notificationMgr = $notificationMgr;
        $this->notificationMgr->setNotificationData($config->get('notification')['data']);
        $this->triggers->setNotificationDelay($config->get('notification_delay_in_hours'));
        $this->triggers->setDb($db);
        $this->addObservers($config->get('notification')['services'], $serviceFactory);
    }

    private function addObservers($observers, ServiceFactory $serviceFactory)
    {
        foreach ($observers as $observer) {
            try {
                $service = $serviceFactory->getService($observer);
                $this->triggers->addObserver($service);
            } catch (\Exception $e) {
                $this->triggers->popObserver();
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
        $this->triggers->checkTriggers($serverData, $msInHour);
    }
}
