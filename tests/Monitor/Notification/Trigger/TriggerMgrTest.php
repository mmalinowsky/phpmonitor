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

        $this->serviceRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->serviceRepository->method('findOneBy')
            ->willReturn($service);

        $notificationLogService = $this->getMockBuilder('Monitor\Service\NotificationLog')
            ->disableOriginalConstructor()
            ->getMock();

        $this->triggers = new TriggerMgr(
            $notificationMgr,
            new PercentageHelper,
            $triggerRepository,
            $this->serviceRepository,
            $notificationLogService
        );
        $this->triggers->setComparator(new Comparator\Comparator);
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
