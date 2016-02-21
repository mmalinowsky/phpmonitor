<?php
namespace Monitor\Config;

interface ConfigInterface
{
    public function loadFromFile($filename);
    public function get($name, $default);
}
