<?php
namespace Monitor\Client\Http;

use \Monitor\Client\ClientInterface;

class Http implements ClientInterface
{
    
    private $curlHandler;
    private $query;
    private $timeout;

    public function __construct()
    {
        if (!is_callable('curl_init')) {
            throw new Exception('Curl function is not callable');
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
    public function setQuery($url, $query)
    {
        $url = $url.'serverinfo';
        $fullPath = $this->fullPath($url, $query);
        curl_setopt($this->curlHandler, CURLOPT_URL, $fullPath);
        $this->query = http_build_query($query);
    }

    /**
     * Making full url path
     *
     * @access private
     * @param $url
     * @param query
     * @return $fullPath
     */
    private function fullPath($url, $query)
    {
        $fullPath = $url.implode('/', $query);
        return $fullPath;
    }

    /**
     * Retrieve resources by http protocol
     *
     * @access public
     * @return mixed
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
