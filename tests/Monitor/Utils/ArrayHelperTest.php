<?php
namespace Monitor\Utils;


class ArrayHelperTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->arrayHelper = new ArrayHelper;
        $this->struct = ['one', 'test2'];
        $this->arrayToFill = ['one' => 1, 'two' => 2, 'test' => 5];
    }

    public function testFillingArrayWithDefaultValue()
    {
        $endArray = ['one' => 1, 'test2' => 0, 'two' => 2, 'test' => 5];

        $ret = $this->invokeMethod(
            $this->arrayHelper,
            'fillWithDefaultValue',
            [
                $this->struct,
                $this->arrayToFill
            ]
        );
        $this->assertSame($ret, $endArray);
    }

    public function testFillingArrayWithCustomString()
    {
        $defaultString = 'StringTest';
        $endArray = ['one' => 1, 'test2' => $defaultString, 'two' => 2, 'test' => 5];

        $ret = $this->invokeMethod(
            $this->arrayHelper,
            'fillWithDefaultValue',
            [
                $this->struct,
                $this->arrayToFill,
                $defaultString
            ]
        );
        $this->assertSame($ret, $endArray);
    }

    private function invokeMethod(&$object, $methodName, $args = [])
    {
        $reflection = new \ReflectionMethod($object, $methodName);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($object, $args);
    }
}