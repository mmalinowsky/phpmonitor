<?php
namespace Monitor\Notification;

use Monitor\Notification\Trigger\Triggers;
use Monitor\Notification\Trigger\Comparator\Comparator;
use Monitor\Database\DatabaseInterface;
use Monitor\Config;
use Monitor\Notification\Service\Factory as ServiceFactory;

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
        ServiceFactory $serviceFactory
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
        foreach ($notificationsData as $notificationData) {
            $notification = new Notification($notificationData);
            $this->notificationMgr->addNotification($notification);
        }
    }

    private function addObservers($observers, $serviceFactory)
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

    public function addTriggers()
    {
        $triggersData = $this->db->getNotificationTriggers();
        $this->triggers->addTriggersByArray($triggersData);
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
