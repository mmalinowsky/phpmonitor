<?php
namespace Monitor\Format;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->rawData = '{"status":"online","hostname":"test","sys_load":0.13,"cpu_cores":4,"disk_free":54121594880}';
    }

    public function testJsonFormatter()
    {
        $factory = new Factory;
        $format = $factory->build('json');
        $data = $format->convertToArray($this->rawData);
        $this->assertTrue(is_array($data));
        $this->assertCount(5, $data);
    }
}
