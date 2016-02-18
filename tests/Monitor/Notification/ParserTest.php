<?php
namespace Monitor\Notification;

use Monitor\Notification\Parser;
use Monitor\Model\Notification;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->notification = $this->getMockBuilder('Monitor\Model\Notification')
            ->getMock();
    }
    
    public function testNotificationParsing()
    {
        $replacement = [
            'bracket' => '123',
            'Test_Var153' => 'Var2'
        ];

        $message = 'Testing {bracket} test abc {Test_Var153} {unknown}';
        $finalMessage = 'Testing 123 test abc Var2 {unknown}';
        
        $this->notification->method('getMessageTemplate')
            ->willReturn($message);

        $this->notification
            ->expects($this->any())->method('setMessage')->will($this->returnCallback(function($message) {
                $this->notification->method('getMessage')
                    ->willReturn($message);
            }));

            
        $parser = new Parser;
        $parser->parse($this->notification, $replacement);
        $this->assertSame($finalMessage, $this->notification->getMessage());
    }
}
