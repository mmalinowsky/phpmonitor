<?php
namespace Monitor\Model;

/**
 * @Entity @Table(name="notification_logs")
 **/
class NotificationLog
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;
    /**
     * @Column(type="integer")
     **/
    private $trigger_id;
    /**
     * @Column(type="integer")
     **/
    private $server_id;
    /**
     * @Column(type="string")
     **/
    private $message;
    /**
     * @Column(type="integer")
     **/
    private $created;

    public function getId()
    {
        return $this->id;
    }

    public function getTriggerId()
    {
        return $this->trigger_id;
    }

    public function getServerId()
    {
        return $this->server_id;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setTriggerId($triggerId)
    {
        $this->trigger_id = $triggerId;
    }

    public function setServerId($serverId)
    {
        $this->server_id = $serverId;
    }
    public function setMessage($text)
    {
        $this->message = $text;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }
}
