<?php
namespace Monitor\Notification\Trigger;

use Monitor\Utils\PercentageHelper;

class TriggersTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $notificationMgr = $this->getMockBuilder('Monitor\Notification\NotificationMgr')
            ->disableOriginalConstructor()
            ->getMock();
        $this->triggers = new Triggers($notificationMgr, new PercentageHelper);
        $this->comparator = new Comparator\Comparator;
        $this->triggers->setComparator($this->comparator);
        $this->services = [
            'load' => 
            [
                'name' => 'Load',
                'sub' => 'Cpu load',
                'percentages' => true,
                'dbcolumns' => 'sys_load:cpu_cores',
                'resize' => false
            ]
        ];
    }

    private function prepareTriggerData($operator, $value, $serviceName, $type = 'service')
    {
        return  $data = 
        [
            'id' => 1212,
            'notification_id' => 1,
            'value' => $value,
            'name' => 'check load',
            'service_name' => $serviceName,
            'operator' => $operator,
            'type' => $type
        ];
    }

    public function testShouldTriggerServiceBeFired()
    {
        $trigger = new Trigger($this->prepareTriggerData('>', 10, 'load'));
        $serverData = ['sys_load' => 0.2, 'cpu_cores' => 1];
        $ret = $this->triggers->shouldTriggerBeFired($trigger, $serverData, $this->services);
        $this->assertTrue($ret);
    }

    public function testShouldTriggerServiceBeFiredFalse()
    {
        $trigger = new Trigger($this->prepareTriggerData('<', 10, 'load'));
        $serverData = ['sys_load' => 0.5, 'cpu_cores' => 1];
        $ret = $this->triggers->shouldTriggerBeFired($trigger, $serverData, $this->services);
        $this->assertFalse($ret);
    }

    public function testShouldTriggersBeFiredByStructCheck()
    {
        $trigger = new Trigger($this->prepareTriggerData('<', 10, 'sys_load', 'struct'));
        $serverData = ['sys_load' => 9];
        $ret = $this->triggers->shouldTriggerBeFired($trigger, $serverData, $this->services);
        $this->assertTrue($ret);
    }
}
