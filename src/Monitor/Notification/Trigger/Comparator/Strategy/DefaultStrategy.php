<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Notification\Trigger\Trigger;
use Monitor\Notification\Trigger\Comparator\ComparatorInterface;

class DefaultStrategy implements StrategyInterface
{

    public function compare(Trigger $trigger, array $serverData, array $services, ComparatorInterface $comparator)
    {
        if (! isset($serverData[$trigger->getServiceName()])) {
            return false;
        }

        if ($comparator->compare($trigger, $serverData[$trigger->getServiceName()])) {
                return true;
        }
        return false;
    }
}
