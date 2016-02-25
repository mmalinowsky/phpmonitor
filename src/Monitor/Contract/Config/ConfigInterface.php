<?php
namespace Monitor\Contract\Config;

interface ConfigInterface
{
    public function __construct($filename = null, array $configValues = []);
    public function get($name, $default = null);
}
