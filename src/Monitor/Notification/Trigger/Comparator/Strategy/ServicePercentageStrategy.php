<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Model\Trigger;
use Monitor\Notification\Trigger\Comparator\ComparatorInterface;
use Monitor\Utils\PercentageHelper;
use Doctrine\ORM\EntityRepository;

class ServicePercentageStrategy implements StrategyInterface
{

    /**
     * Compare trigger value to service percentage
     *
     * @param Monitor\Model\Trigger $trigger
     * @param array $serverData
     * @param \Doctrine\ORM\EntityRepository $serviceRepository
     * @param \Monitor\Utils\PercentageHelper $percentageHelper
     * @param \Monitor\Notification\Trigger\Comparator\ComparatorInterface $comparator
     */
    public function compare(
        Trigger $trigger,
        array $serverData,
        EntityRepository $serviceRepository,
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
