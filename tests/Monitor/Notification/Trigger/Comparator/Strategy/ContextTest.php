<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

class ContextTest extends \PHPUnit_Framework_TestCase
{

    public function testServiceStrategy()
    {
        $strategyName = 'service';
        $strategyContext = new Context($strategyName);
        $strategy = $this->setPropertyAccessible($strategyContext, 'strategy');
        $this->assertTrue(is_object($strategy->getValue($strategyContext)));
    }

    private function setPropertyAccessible(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty;
    }
}
