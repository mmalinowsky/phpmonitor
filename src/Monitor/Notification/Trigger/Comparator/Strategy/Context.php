<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Model\Trigger;
use Monitor\Notification\Trigger\Comparator\Comparator;
use Monitor\Utils\PercentageHelper;

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

    public function compare(
        Trigger $trigger,
        array $serverData,
        $serviceRepository,
        PercentageHelper $percentageHelper,
        Comparator $comparator
    ) {
        return $this->strategy->compare(
            $trigger,
            $serverData,
            $serviceRepository,
            $percentageHelper,
            $comparator
        );
    }

    public function getStrategy()
    {
        return $this->strategy;
    }
}
