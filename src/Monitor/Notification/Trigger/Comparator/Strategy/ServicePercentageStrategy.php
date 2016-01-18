<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Notification\Trigger\Trigger;
use Monitor\Notification\Trigger\Comparator\Comparator;

class ServicePercentageStrategy implements StrategyInterface
{
    public function compare(Trigger $trigger, array $serverData, array $services, Comparator $comparator)
    {
        if ($comparator->compare($trigger, servicePercentage($serverData, $services[$trigger->getServiceName()]))) {
            return true;
        }
        return false;
    }
}
