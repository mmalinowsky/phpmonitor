<?php
namespace Monitor\Notification\Trigger;

class Trigger
{
    
    private $id;
    private $notificationId;
    private $value;
    private $name;
    private $serviceName;
    private $operator;
    private $type;

    public function __construct($data)
    {
        if (!is_array($data)) {
            return;
        }
        
        $this->id = $data['id'];
        $this->notificationId = $data['notification_id'];
        $this->value = $data['value'];
        $this->name = $data['name'];
        $this->serviceName = $data['service_name'];
        $this->operator = $data['operator'];
        $this->type = $data['type'];
    }
    
    /**
     * Return properties
     * @return array
     */
    public function toArray()
    {
        return [
            'triggerValue' => $this->value,
            'serviceName' => $this->serviceName,
            'operator' => $this->operator
        ];
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getNotificationId()
    {
        return $this->notificationId;
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
