<?php
namespace Monitor\Notification\Service;

class Factory
{

    public function getService($className)
    {
        $className = __NAMESPACE__.'\\'.$className;
        if (! class_exists($className)) {
            throw new \Exception($className.' Service class not found');
        }
        return new $className;
    }
}
