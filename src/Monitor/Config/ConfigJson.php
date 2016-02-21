<?php
namespace Monitor\Config;

class ConfigJson implements ConfigInterface
{

    private $data;

    public function __construct($filename = null, array $configValues = [])
    {
        $this->data = $configValues;

        if ($filename) {
            $this->loadFromFile($filename);
        }
    }

    private function loadFromFile($filename)
    {
        $fullPath = $this->getFullPath($filename);
        $this->isFileReadable($fullPath);
        $configData = file_get_contents($fullPath);
        $this->data = $this->decode($configData);
    }

    public function get($name, $default = null)
    {
        if (isset($this->data[$name])
          || $default) {
            return isset($this->data[$name]) ? $this->data[$name] : $default;
        }
        throw new \Exception($name.' not found in Config');
    }

    private function getFullPath($filename)
    {
        $fullPath = __DIR__.'/../'.$filename;
        return $fullPath;
    }

    /**
     * Decode json string to array
     *
     * @param  $data
     * @return array $decodedData
     */
    private function decode($data)
    {
        $decodedData = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Can\'t parse config');
        }
        return (array) $decodedData;
    }

    /**
     *
     * @param fullPath
     */
    private function isFileReadable($fullPath)
    {
        if (! is_readable($fullPath)) {
            throw new \Exception('config is not readable');
        }
    }
}
