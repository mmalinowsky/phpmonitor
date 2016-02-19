<?php
namespace Monitor\Notification\Trigger;

use Monitor\Utils\PercentageHelper;

class TriggerMgrTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $notificationMgr = $this->getMockBuilder('Monitor\Notification\NotificationMgr')
            ->disableOriginalConstructor()
            ->getMock();

        $triggerRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $triggerRepository->method('findOneBy')
            ->with($this->anything())
            ->willReturn($this->prepareTrigger('>', 10, 'load'));


$this->service = $this->getMockBuilder('Monitor\Model\Service')
            ->disableOriginalConstructor()
            ->getMock();
        $this->service->method('getDBColumns')
            ->willReturn('sys_load:cpu_cores');

 $this->serviceRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->serviceRepository->method('getDBColumns')
            ->willReturn('sys_load:cpu_cores');
        $this->serviceRepository->method('getPercentages')
            ->willReturn(true);
        $this->serviceRepository->method('findOneBy')
            ->willReturn($this->service);
        $this->triggers = new TriggerMgr($notificationMgr, new PercentageHelper, $this->serviceRepository, $triggerRepository);
        $this->comparator = new Comparator\Comparator;
        $this->triggers->setComparator($this->comparator);
    }

    private function prepareTrigger($operator, $value, $serviceName, $type = 'service')
    {
        $trigger = $this->getMockBuilder('Monitor\Model\Trigger')
            ->disableOriginalConstructor()
            ->getMock();

        $trigger->method('getValue')
            ->willReturn($value);
        $trigger->method('getOperator')
            ->willReturn($operator);
        $trigger->method('getServiceName')
            ->willReturn($serviceName);
        $trigger->method('getType')
            ->willReturn($type);
        return $trigger;
    }
    public function testShouldTriggerServiceBeFired()
    {
        $trigger = $this->prepareTrigger('>', 10, 'Cpu Load');
        $serverData = ['sys_load' => 0.2, 'cpu_cores' => 1];
        $ret = $this->triggers->shouldTriggerBeFired($trigger, $serverData, $this->serviceRepository);
        $this->assertTrue($ret);
    }

    public function testShouldTriggerServiceBeFiredFalse()
    {
        $trigger = $this->prepareTrigger('<', 10, 'Cpu Load');
        $serverData = ['sys_load' => 0.5, 'cpu_cores' => 1];
        $ret = $this->triggers->shouldTriggerBeFired($trigger, $serverData, $this->serviceRepository);
        $this->assertFalse($ret);
    }

    public function testShouldTriggersBeFiredByStructCheck()
    {
        $trigger = $this->prepareTrigger('<', 10, 'sys_load', 'struct');
        $serverData = ['sys_load' => 9];
        $ret = $this->triggers->shouldTriggerBeFired($trigger, $serverData, $this->serviceRepository);
        $this->assertTrue($ret);
    }

    public function setPropertyAccessible(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty;
    }
}
