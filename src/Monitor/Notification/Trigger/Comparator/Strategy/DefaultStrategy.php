<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Model\Trigger;
use Monitor\Notification\Trigger\Comparator\ComparatorInterface;
use Monitor\Utils\PercentageHelper;

class DefaultStrategy implements StrategyInterface
{

    public function compare(
        Trigger $trigger,
        array $serverData,
        $serviceRepository,
        PercentageHelper $percentageHelper,
        ComparatorInterface $comparator
    ) {
        if (! isset($serverData[$trigger->getServiceName()])) {
            return false;
        }

        if ($comparator->compare($trigger, $serverData[$trigger->getServiceName()])) {
                return true;
        }
        return false;
    }
}
