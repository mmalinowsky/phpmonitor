<?php
namespace Monitor\Client\Http;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    
    public function testGettingFullPath()
    {
        $url = 'localhost/serverinfo/';
        $query['format']= 'json';
        $query['hostname']= 'site.com';
        $fullUrl = $url.$query['format'].'/'.$query['hostname'];
        $client = new Http;
        $methodRet = $this->invokeMethod($client, 'fullPath', [$url, $query]);
        $this->assertSame($methodRet, $fullUrl);
    }

    private function invokeMethod(&$object, $methodName, $args = [])
    {
        $reflection = new \ReflectionMethod($object, $methodName);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($object, $args);
    }
}