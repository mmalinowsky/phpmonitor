<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Model\Trigger;
use Monitor\Notification\Trigger\Comparator\ComparatorInterface;
use Monitor\Utils\PercentageHelper;

class ServicePercentageStrategy implements StrategyInterface
{
    public function compare(Trigger $trigger, array $serverData, array $services, PercentageHelper $percentageHelper, ComparatorInterface $comparator)
    {
        $serviceKey = md5($trigger->getServiceName());
        if(!isset($services[$serviceKey])) {
            //$log->error('cant find service')
            return false;
        }
        $serviceCompare = $percentageHelper->getServicePercentage($serverData, $services[$serviceKey]);
        if ($comparator->compare($trigger, $serviceCompare)) {
            return true;
        }
        return false;
    }
}
