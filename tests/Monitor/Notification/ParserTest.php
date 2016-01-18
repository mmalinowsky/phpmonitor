<?php
namespace Monitor\Notification;

use Monitor\Notification\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->notificationData = [
            'message' => 'Testing {bracket} test abc {Test_Var153} {unknown}',
            'id' => 10
        ];
    }
    
    public function testNotificationParsing()
    {
        $replacement = [
            'bracket' => '123',
            'Test_Var153' => 'Var2'
        ];
        $endMessage = 'Testing 123 test abc Var2 {unknown}';
        $parser = new Parser;
        $notification = new Notification($this->notificationData);
        $parser->parse($notification, $replacement);
        $this->assertSame($notification->getMessage(), $endMessage);
    }
}
