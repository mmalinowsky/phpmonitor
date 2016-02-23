<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Model\Trigger;
use Monitor\Notification\Trigger\Comparator\ComparatorInterface;
use Monitor\Utils\PercentageHelper;
use Doctrine\ORM\EntityRepository;

interface StrategyInterface
{

    public function compare(
        Trigger $trigger,
        array $serverData,
        EntityRepository $serviceRepository,
        PercentageHelper $percentageHelper,
        ComparatorInterface $comparator
    );
}
