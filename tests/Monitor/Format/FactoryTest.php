<?php
namespace Monitor\Format;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testBuildingFormatParser()
    {
        $factory = new Factory;
        $formatParser = $factory->build('json');
        $this->assertTrue(is_object($formatParser));
    }
}
