<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Notification\Trigger\Trigger;
use Monitor\Notification\Trigger\Comparator\ComparatorInterface;
use Monitor\Utils\PercentageHelper;

class ServicePercentageStrategy implements StrategyInterface
{
    public function compare(Trigger $trigger, array $serverData, array $services, PercentageHelper $percentageHelper, ComparatorInterface $comparator)
    {
        $serviceCompare = $percentageHelper->getServicePercentage($serverData, $services[$trigger->getServiceName()]);
        if ($comparator->compare($trigger, $serviceCompare)) {
            return true;
        }
        return false;
    }
}
