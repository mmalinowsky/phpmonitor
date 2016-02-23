<?php
namespace Monitor\Notification\Trigger\Comparator;

use Monitor\Model\Trigger;

class Comparator implements ComparatorInterface
{

    /**
     * Comparing trigger's value
     *
     * @access public
     * @param  \Monitor\Model\Trigger $trigger
     * @param  $value
     * @return bool
     */
    public function compare(Trigger $trigger, $value)
    {
        switch ($trigger->getOperator()) {
            case ">":
                return $this->moreThan($value, $trigger->getValue());

            case "<":
                return ! $this->moreThan($value, $trigger->getValue());

            case "=":
                return $this->equal($value, $trigger->getValue());

            case "!=":
                return ! $this->equal($value, $trigger->getValue());
        }
        return false;
    }

    /**
     * Check if $value is higher than $value2
     *
     * @param $value
     * @param $value2
     * @return bool
     */
    private function moreThan($value, $value2)
    {
        if ($value > $value2) {
            return true;
        }
        return false;
    }

    /**
     * Check if $value is equal to $value2
     *
     * @param $value
     * @param $value2
     * @return bool
     */
    private function equal($value, $value2)
    {
        if ($value == $value2) {
            return true;
        }
        return false;
    }
}
