<?php
namespace Monitor\Utils;


class PercentageHelperTest extends \PHPUnit_Framework_TestCase
{

    public function testServicePercentage()
    {
        $helper = new PercentageHelper;
        $service = $this->getMockBuilder('Monitor\Model\Service')
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getDBColumns')
            ->willReturn('memoryUsage:memoryTotal');
        $serverData = ['memoryTotal' => 1024, 'memoryUsage' => 512];
        $percent = $helper->getServicePercentage($serverData, $service);
        $this->assertSame((int)$percent, 50);
    }
}