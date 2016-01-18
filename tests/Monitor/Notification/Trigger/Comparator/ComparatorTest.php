<?php
namespace Monitor\Notification\Trigger\Comparator;

use Monitor\Notification\Trigger\Trigger;

class ComparatorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->comparator = new Comparator;
    }

    private function prepareTriggerData($operator, $value)
    {
        return  $data = [
            'id' => 1212,
            'notification_id' => 1,
            'value' => $value,
            'name' => 'unknown',
            'service_name' => 'unknownServiceName',
            'operator' => $operator,
            'type' =>'service'
        ];
    }

    public function testIsValueIsHigher()
    {
        $valueToCompare = 50;
        $data = $this->prepareTriggerData('>', 10);
        $trigger = new Trigger($data);

        $this->assertTrue($this->comparator->compare($trigger, $valueToCompare));
    }

    public function testIsValueIsEqual()
    {
        $valueToCompare = 50;
        $data = $this->prepareTriggerData('=', 50);
        $trigger = new Trigger($data);

        $this->assertTrue($this->comparator->compare($trigger, $valueToCompare));
    }

    public function testIsValueIsLower()
    {
        $valueToCompare = 30;
        $data = $this->prepareTriggerData('<', 50);
        $trigger = new Trigger($data);

        $this->assertTrue($this->comparator->compare($trigger, $valueToCompare));
    }

    public function testIsValueIsDifferent()
    {
        $valueToCompare = 50;
        $data = $this->prepareTriggerData('!=', 50);
        $trigger = new Trigger($data);

        $this->assertFalse($this->comparator->compare($trigger, $valueToCompare));
    }
}
