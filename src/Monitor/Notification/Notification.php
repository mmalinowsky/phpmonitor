<?php
namespace Monitor\Notification;

class Notification
{
    
    private $id;
    private $messageTemplate = '';
    private $message = '';

    public function __construct(array $data)
    {
        $this->messageTemplate = $data['message'];
        $this->id = $data['id'];
    }

    public function getId()
    {
        return $this->id;
    }
    public function getMessageTemplate()
    {
        return $this->messageTemplate;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($text)
    {
        $this->message = $text;
    }
}
