<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Model\Trigger;
use Monitor\Contract\Notification\Trigger\Comparator\ComparatorInterface;
use Monitor\Utils\PercentageHelper;
use Doctrine\ORM\EntityRepository;
use Monitor\Contract\Notification\Trigger\Comparator\Strategy\StrategyInterface;

class DefaultStrategy implements StrategyInterface
{

    /**
     * Compare trigger value to $serverData[$servicename]
     *
     * @param Monitor\Model\Trigger $trigger
     * @param array $serverData
     * @param \Doctrine\ORM\EntityRepository $serviceRepository
     * @param \Monitor\Utils\PercentageHelper $percentageHelper
     * @param \Monitor\Contract\Notification\Trigger\Comparator\ComparatorInterface $comparator
     */
    public function compare(
        Trigger $trigger,
        array $serverData,
        EntityRepository $serviceRepository,
        PercentageHelper $percentageHelper,
        ComparatorInterface $comparator
    ) {
        if (! isset($serverData[$trigger->getServiceName()])) {
            return false;
        }

        if ($comparator->compare(
            $trigger,
            $serverData[$trigger->getServiceName()]
        )) {
            return true;
        }
        return false;
    }
}
