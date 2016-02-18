<?php
namespace Monitor\Notification\Trigger\Comparator;

use Monitor\Model\Trigger;

interface ComparatorInterface
{
    public function compare(Trigger $trigger, $value);
}
