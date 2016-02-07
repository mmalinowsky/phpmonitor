<?php
namespace Monitor\Format;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->rawData = '<?xml version="1.0"?>
        <root>
        <status>online</status><hostname>laptop</hostname><sys_load>0.21</sys_load><cpu_cores>4</cpu_cores><disk_free>53989769216</disk_free>
        </root>';
    }

    public function testXmlFormatter()
    {
        $factory = new Factory;
        $format = $factory->build('xml');
        $data = $format->convertToArray($this->rawData);
        $this->assertTrue(is_array($data));
        $this->assertCount(5, $data);
    }
}
