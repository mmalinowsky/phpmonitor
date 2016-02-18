<?php
namespace Monitor\Notification\Trigger\Comparator;

use Monitor\Model\Trigger;

class ComparatorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->comparator = new Comparator;
    }

    private function prepareTrigger($operator, $value)
    {
        $trigger = $this->getMockBuilder('Monitor\Model\Trigger')
            ->disableOriginalConstructor()
            ->getMock();

        $trigger->method('getValue')
            ->willReturn($value);

        $trigger->method('getOperator')
            ->willReturn($operator);

        return $trigger;
    }

    public function testIsValueIsHigher()
    {
        $valueToCompare = 50;
        $trigger =  $this->prepareTrigger('>', 10);

        $this->assertTrue($this->comparator->compare($trigger, $valueToCompare));
    }

    public function testIsValueIsEqual()
    {
        $valueToCompare = 50;
        $trigger = $this->prepareTrigger('=', 50);

        $this->assertTrue($this->comparator->compare($trigger, $valueToCompare));
    }

    public function testIsValueIsLower()
    {
        $valueToCompare = 30;
        $trigger = $this->prepareTrigger('<', 50);

        $this->assertTrue($this->comparator->compare($trigger, $valueToCompare));
    }

    public function testIsValueIsDifferent()
    {
        $valueToCompare = 50;
        $trigger = $this->prepareTrigger('!=', 50);

        $this->assertFalse($this->comparator->compare($trigger, $valueToCompare));
    }
}
