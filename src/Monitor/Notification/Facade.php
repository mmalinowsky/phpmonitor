<?php
namespace Monitor\Notification;

use Monitor\Notification\Trigger\Triggers;
use Monitor\Notification\Trigger\Comparator\Comparator;
use Monitor\Database\DatabaseInterface;
use Monitor\Config;

class Facade
{
    private $notificationMgr;
    private $triggers;
    private $services;
    private $db;

    public function __construct(
        Config $config,
        DatabaseInterface &$db,
        NotificationMgr $notificationMgr,
        Triggers $triggers,
        Service\Factory $serviceFactory
    ) {
        $this->db = $db;
        $this->triggers = $triggers;
        $this->services = $db->getServices();
        $this->notificationMgr = $notificationMgr;
        $this->notificationMgr->setNotificationData($config->get('notification')['data']);
        $this->triggers->setNotificationDelay($config->get('notification_delay_in_hours'));
        $this->triggers->setDb($db);
        $this->addNotifications();
        $this->addTriggers();
        $this->addObservers($config->get('notification')['services'], $serviceFactory);
    }

    public function addNotifications()
    {
        $notificationsData = $this->db->getNotifications();
        foreach ($notificationsData as $notification) {
            $this->notificationMgr->addNotification(new Notification($notification));
        }
    }

    private function addObservers($observers, $serviceFactory)
    {
        foreach ($observers as $observer) {
            try {
                $this->triggers->addObserver($serviceFactory->getService($observer));
            } catch (\Exception $e) {
                $this->triggers->popObserver();
            }
        }
    }

    public function addTriggers()
    {
        $this->triggers->addTriggersByArray($this->db->getNotificationTriggers());
    }

    /**
     * Check triggers for concrete server
     *
     * @param array $serverData
     */
    public function checkTriggers(array $serverData)
    {
        $this->triggers->checkTriggers($serverData, $this->services);
    }
}
