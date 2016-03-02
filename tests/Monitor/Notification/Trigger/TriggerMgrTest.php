<?php
namespace Monitor\Notification\Trigger;

use Monitor\Utils\PercentageHelper;

class TriggerMgrTest extends \PHPUnit_Framework_TestCase
{
    
    public function setUp()
    {
        $service = $this->getMockBuilder('Monitor\Model\Service')
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getName')
            ->willReturn('Cpu Load');
        $service->method('getDBColumns')
            ->willReturn('sys_load:cpu_cores');
        $service->method('getPercentages')
            ->willReturn(1);

        $notificationMgr = $this->getMockBuilder('Monitor\Notification\NotificationMgr')
            ->disableOriginalConstructor()
            ->getMock();
        $notificationMgr->method('hasNotificationDelayExpired')
            ->willreturn('true');
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
            $notificationLogService,
            new Comparator\Comparator
        );
        $this->msDelay = 3600;
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
        $serverData = ['sys_load' => 0.2, 'cpu_cores' => 1, 'server_id' => 1];
        $ret = $this->triggers->shouldTriggerBeFired($trigger, $serverData, $this->msDelay);
        $this->assertTrue($ret);
    }

    public function testShouldTriggerServiceBeFiredFalse()
    {
        $trigger = $this->prepareTrigger('<', 10, 'Cpu Load');
        $serverData = ['sys_load' => 0.5, 'cpu_cores' => 1, 'server_id' => 1];
        $ret = $this->triggers->shouldTriggerBeFired($trigger, $serverData, $this->msDelay);
        $this->assertFalse($ret);
    }

    public function testShouldTriggersBeFiredByStructCheck()
    {
        $trigger = $this->prepareTrigger('<', 10, 'sys_load', 'struct');
        $serverData = ['sys_load' => 9, 'server_id' => 1];
        $ret = $this->triggers->shouldTriggerBeFired($trigger, $serverData, $this->msDelay);
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
