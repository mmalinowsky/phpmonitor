<?php
namespace Monitor\Format;

class Factory
{
    
    public function build($type)
    {
        $className = 'Monitor\Format\\' . ucwords($type);
        if (!class_exists($className)) {
            throw new \Exception($className.' format class not found');
        }
        return new $className;
    }
}
