<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Model\Trigger;
use Monitor\Notification\Trigger\Comparator\ComparatorInterface;
use Monitor\Utils\PercentageHelper;

interface StrategyInterface
{
    public function compare(Trigger $trigger, array $serverData, $serviceRepository, PercentageHelper $percentageHelper, ComparatorInterface $comparator);
}
