<?php
namespace Monitor\Notification;

use Monitor\Notification\Trigger\TriggerMgr;
use Monitor\Config\ConfigInterface;
use Monitor\Notification\Service\Factory as ServiceFactory;

class Facade
{

    /**
     * Trigger manager
     * @var Monitor\Notification\Trigger\TriggerMgr
     */
    private $triggerMgr;

    public function __construct(
        ConfigInterface $config,
        TriggerMgr $triggerMgr,
        ServiceFactory $serviceFactory
    ) {
        $this->triggerMgr = $triggerMgr;
        $this->addObservers($config->get('notification')['services'], $serviceFactory);
    }

    /**
     * Add observers
     *
     * @param array $observers
     * @param \Monitor\Notification\Service\Factory $serviceFactory
     */
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
