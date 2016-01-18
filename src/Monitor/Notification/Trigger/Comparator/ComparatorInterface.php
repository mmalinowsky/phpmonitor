<?php
namespace Monitor\Notification\Trigger\Comparator;

use Monitor\Notification\Trigger\Trigger;

interface ComparatorInterface
{
    public function compare(Trigger $trigger, $value);
}
