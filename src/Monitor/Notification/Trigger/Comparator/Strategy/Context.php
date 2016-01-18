<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Notification\Trigger\Trigger;

class Context
{
    private $strategy;

    public function __construct($strategyName)
    {
        switch ($strategyName) {
            case 'service':
                $this->strategy = new ServicePercentageStrategy();
                break;
            default:
                $this->strategy = new DefaultStrategy();
                break;
        }
    }

    public function compare($trigger, array $serverData, $service, $comparator)
    {
        return $this->strategy->compare($trigger, $serverData, $service, $comparator);
    }

    public function getStrategy()
    {
        return $this->strategy;
    }
}
