<?php
namespace Monitor\Notification;

use Monitor\Contract\Notification\Service\ServiceInterface;
use Monitor\Model\Notification;

abstract class Observable
{

    protected $observers;

    public function addObserver(ServiceInterface $observer)
    {
        $this->observers[] = $observer;
    }

    public function popObserver()
    {
        array_pop($this->observers);
    }

    abstract public function notifyServices(Notification $notification);
}
