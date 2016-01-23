<?php
namespace Monitor\Client;

use Monitor\Client\Exception\HttpException;

class Http implements ClientInterface
{
    
    private $curlHandler;
    private $query;
    private $timeout;

    public function __construct()
    {
        if (!is_callable('curl_init')) {
            throw new HttpException('Curl function is not callable');
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
        curl_setopt($this->curlHandler, CURLOPT_URL, $url.'get/serverinfo/'. $query['format']. '/'. $query['ping_host']);
        $this->query = http_build_query($query);
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
