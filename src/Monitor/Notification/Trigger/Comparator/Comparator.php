<?php
namespace Monitor\Notification\Trigger\Comparator;

use Monitor\Model\Trigger;

class Comparator implements ComparatorInterface
{

    /**
     * Comparing trigger's value
     *
     * @access public
     * @param  $trigger
     * @param  $value
     * @return boolean
     */
    public function compare(Trigger $trigger, $value)
    {
        switch ($trigger->getOperator()) {
            case ">":
                return $this->moreThan($value, $trigger->getValue());

            case "<":
                return $this->lessThan($value, $trigger->getValue());

            case "=":
                return $this->equal($value, $trigger->getValue());

            case "!=":
                return ! $this->equal($value, $trigger->getValue());
        }
        return false;
    }

    private function lessThan($value, $value2)
    {
        if ($value < $value2) {
            return true;
        }
        return false;
    }

    private function moreThan($value, $value2)
    {
        if ($value > $value2) {
            return true;
        }
        return false;
    }

    private function equal($value, $value2)
    {
        if ($value == $value2) {
            return true;
        }
        return false;
    }
}
