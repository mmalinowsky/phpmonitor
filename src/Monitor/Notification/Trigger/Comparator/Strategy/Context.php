<?php
namespace Monitor\Notification\Trigger\Comparator\Strategy;

use Monitor\Model\Trigger;
use Monitor\Notification\Trigger\Comparator\Comparator;
use Monitor\Utils\PercentageHelper;
use Doctrine\ORM\EntityRepository;

class Context
{
    /**
     * @var \Monitor\Contract\Notification\Trigger\Comparator\Strategy\Strategy
     */
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

    /**
     * Use strategy to compare trigger value
     *
     * @param Monitor\Model\Trigger $trigger
     * @param array $serverData
     * @param \Doctrine\ORM\EntityRepository $serviceRepository
     * @param \Monitor\Utils\PercentageHelper $percentageHelper
     * @param \Monitor\Contract\Notification\Trigger\Comparator\ComparatorInterface $comparator
     */
    public function compare(
        Trigger $trigger,
        array $serverData,
        EntityRepository $serviceRepository,
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
}
