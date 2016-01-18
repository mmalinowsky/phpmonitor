<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testServiceStrategy()
    {
        $strategyName = 'service';
        $strategyContext = new Context($strategyName);
        $this->assertTrue(is_object($strategyContext->getStrategy()));
    }
}
