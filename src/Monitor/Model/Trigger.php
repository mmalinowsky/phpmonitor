<?php
namespace Monitor\Model;

/**
 * @Entity @Table(name="notification_triggers")
 **/
class Trigger
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;
    /**
     * @Column(type="integer")
    **/
    private $notification_id;
    /**
     * @Column(type="integer")
     **/
    private $value;
    /**
     * @Column(type="string")
    **/
    private $name;
    /**
     * @Column(type="string")
    **/
    private $serviceName;
    /**
     * @Column(type="string")
    **/
    private $operator;
    /**
     * @Column(type="string")
    **/
    private $type;
    
    /**
     * Return properties
     * @return array
     */
    public function toArray()
    {
        return [
            'triggerName'   => $this->name,
            'triggerValue'  => $this->value,
            'serviceName'   => $this->serviceName,
            'operator'      => $this->operator
        ];
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getNotificationId()
    {
        return $this->notification_id;
    }
    
    public function getServiceName()
    {
        return $this->serviceName;
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getOperator()
    {
        return $this->operator;
    }
}
