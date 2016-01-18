<?php
namespace Monitor;

class MonitorTest extends \PHPUnit_Framework_TestCase
{
    public function testResourceParse()
    {
        $array = ['test' => 'datatest', 'abc' => 'cba'];
        $facade = $this->getMockBuilder('Monitor\Notification\Facade')->disableOriginalConstructor()->getMock();
        $db = $this->getMockBuilder('Monitor\Database\PdoSimple')->disableOriginalConstructor()->getMock();
        $monitor = new Monitor(array(), $db, $facade);
        //$monitor->resourceParse(json_encode($array));
    }
}
