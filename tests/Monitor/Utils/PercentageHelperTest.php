<?php
namespace Monitor\Utils;


class PercentageHelperTest extends \PHPUnit_Framework_TestCase
{

    public function testServicePercentage()
    {
        $helper = new PercentageHelper;
        $service = ['dbcolumns' => 'memoryUsage:memoryTotal'];
        $serverData = ['memoryTotal' => 1024, 'memoryUsage' => 512];
        $percent = $helper->getServicePercentage($serverData, $service);
        $this->assertSame((int)$percent, 50);
    }
}