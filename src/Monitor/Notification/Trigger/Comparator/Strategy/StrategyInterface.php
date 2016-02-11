<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Notification\Trigger\Trigger;
use Monitor\Notification\Trigger\Comparator\ComparatorInterface;

interface StrategyInterface
{
    public function compare(Trigger $trigger, array $serverData, array $services, ComparatorInterface $comparator);
}
