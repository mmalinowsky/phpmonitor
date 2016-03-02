<?php
namespace Monitor\Client\Http;

use \Monitor\Contract\Client\ClientInterface;

class Http implements ClientInterface
{

    /**
     * Curl handler
     * @var object
     */
    private $curlHandler;
    /**
     * Curl timeout
     * @var integer
     */
    private $timeout;

    public function __construct()
    {
        if (! is_callable('curl_init')) {
            throw new \Exception('Curl function is not callable');
        }

        $this->curlHandler = curl_init();
        $this->timeout = 3;
    }
    
    /**
     * Setting curl query
     *
     * @access public
     * @param  string $url   api url
     * @param  array  $query
     */
    public function setQuery($query)
    {
        $fullPath = $this->fullPath($query);
        curl_setopt($this->curlHandler, CURLOPT_URL, $fullPath);
    }

    /**
     * Making full url path
     *
     * @access private
     * @param  array  $query
     * @return string $fullPath
     */
    private function fullPath($query)
    {
        $fullPath = implode('/', $query);
        return $fullPath;
    }

    /**
     * Retrieve resources by http protocol
     *
     * @return mixed $resources
     */
    public function getResources()
    {
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curlHandler, CURLOPT_TIMEOUT, $this->timeout);
        $resources = curl_exec($this->curlHandler);
        return $resources;
    }

    /**
     * Setting curl timeout
     *
     * @access public
     * @param  int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function __destruct()
    {
        curl_close($this->curlHandler);
    }
}
