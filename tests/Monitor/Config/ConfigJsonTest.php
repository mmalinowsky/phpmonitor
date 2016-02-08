<?php
namespace Monitor\Config;

class ConfigJsonTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        //todo vfs
    }

    public function testLoadingConfigFromFile()
    {
        $config = new ConfigJson();
        $config->loadFromFile('Config.json');
        $this->assertSame($config->get('format'), 'json');
    }
}