<?php
namespace Monitor;

class MonitorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->config = $this->getMockBuilder('\Monitor\Config')
                             ->setMockClassName('Config')
                             ->getMock();
        $this->db = $this->getMockBuilder('\Monitor\Database\PdoSimple')
                         ->setMockClassName('PdoSimple')
                         ->disableOriginalConstructor()
                         ->getMock();
        $this->notificationFacade = $this->getMockBuilder('\Monitor\Notification\Facade')
                                         ->setMockClassName('Facade')
                                         ->disableOriginalConstructor()
                                         ->getMock();
        $this->monitor = new Monitor($this->config, $this->db, $this->notificationFacade);
        $this->struct = ['one', 'test2'];
        $this->arrayToFill = ['one' => 1, 'two' => 2, 'test' => 5];
    }

    public function testFillingArrayWithDefaultValue()
    {
        $endArray = ['one' => 1, 'test2' => 0, 'two' => 2, 'test' => 5];

        $ret = $this->invokeMethod(
            $this->monitor,
            'fillArrayWithDefaultValue',
            [
                $this->struct,
                $this->arrayToFill
            ]
        );
        $this->assertSame($ret, $endArray);
    }

    public function testFillingArrayWithCustomString()
    {
        $defaultString = 'StringTest';
        $endArray = ['one' => 1, 'test2' => $defaultString, 'two' => 2, 'test' => 5];

        $ret = $this->invokeMethod(
            $this->monitor,
            'fillArrayWithDefaultValue',
            [
                $this->struct,
                $this->arrayToFill,
                $defaultString
            ]
        );
        $this->assertSame($ret, $endArray);
    }

    private function invokeMethod(&$object, $methodName, $args = [])
    {
        $reflection = new \ReflectionMethod($object, $methodName);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($object, $args);
    }
}