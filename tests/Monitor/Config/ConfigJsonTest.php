<?php
namespace Monitor\Config;

class ConfigJsonTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        //todo vfs
        $configData = ['format' => 'json'];
        $this->config = new ConfigJson(null, $configData);
    }

    public function testLoadingConfigFromFile()
    {
        $this->assertSame($this->config->get('format'), 'json');
    }

    public function testGettingInvalidKeyWithDefaultValue()
    {
        $defaultValue = 'defaultValue';
        $configValue= $this->config->get('NonValidKey', $defaultValue);
        $this->assertSame($configValue, $defaultValue);
    }

    /**
     * @expectedException \Exception
     */
    public function testGettingInvalidKeyWithoutDefaultValue()
    {
        $defaultValue = 'defaultValue';
        $configValue= $this->config->get('NonValidKey');
        $this->assertSame($configValue, $defaultValue);
    }
}
