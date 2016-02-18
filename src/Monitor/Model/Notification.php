<?php
namespace Monitor\Model;

/**
 * @Entity @Table(name="notifications")
 **/
class Notification
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;
    /**
     * @Column(type="string")
     **/
    private $messageTemplate;

    private $message = '';

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

    public function setMessageTemplate($message)
    {
        $this->messageTemplate = $message;
    }

    public function setMessage($text)
    {
        $this->message = $text;
    }
}
