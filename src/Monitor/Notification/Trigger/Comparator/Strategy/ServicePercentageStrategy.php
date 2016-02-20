<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Model\Trigger;
use Monitor\Notification\Trigger\Comparator\ComparatorInterface;
use Monitor\Utils\PercentageHelper;

class ServicePercentageStrategy implements StrategyInterface
{
    public function compare(
        Trigger $trigger,
        array $serverData,
        $serviceRepository,
        PercentageHelper $percentageHelper,
        ComparatorInterface $comparator
    ) {
        $service = $serviceRepository->findOneBy(['name' => $trigger->getServiceName()]);

        if (! $service) {
            //$log->error('cant find service')
            return false;
        }
        $serviceCompare = $percentageHelper->getServicePercentage($serverData, $service);
        if ($comparator->compare($trigger, $serviceCompare)) {
            return true;
        }
        return false;
    }
}
