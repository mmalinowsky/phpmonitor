<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Notification\Trigger\Trigger;
use Monitor\Notification\Trigger\Comparator\Comparator;

interface StrategyInterface
{
    public function compare(Trigger $trigger, array $serverData, array $services, Comparator $comparator);
}
