<?php
namespace Monitor\Service;

use Monitor\Model\NotificationLog as NotificationLogModel;
use Doctrine\ORM\EntityManager;

class NotificationLog
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var integer
     */
    private $notificationDelayInHours;

    public function __construct(EntityManager $em, $notificationDelayInHours)
    {
        $this->em = $em;
        $this->notificationDelayInHours = $notificationDelayInHours;
    }

    /**
     * Save NotificationLog
     *
     * @param \Monitor\Model\NotificationLog $notificationLog
     */
    public function save(NotificationLogModel $notificationLog)
    {
        $this->em->persist($notificationLog);
        $this->em->flush();
    }

    /**
     * Get last fired up specific type of trigger for concret server
     *
     * @param int $triggerId
     * @param int $serverId
     */
    public function getLastForTrigger($triggerId, $serverId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('nl.created')
            ->from('Monitor\Model\NotificationLog', 'nl')
            ->where('nl.trigger_id = ?1')
            ->andWhere('nl.server_id = ?2')
            ->orderBy('nl.created', 'DESC')
            ->setMaxResults(1)
            ->setParameters(
                [
                    '1' => $triggerId,
                    '2' => $serverId
                ]
            );
        $query = $queryBuilder->getQuery();
        $queryResult = $query->getResult();
        return $queryResult;
    }
    /**
     * Check if same type of notification for concret server has been sent already
     *
     * @access private
     * @param  int $triggerId
     * @param  int $serverId
     * @param  int $msDelay
     * @return boolean
     */
    public function hasNotificationDelayExpired($triggerId, $serverId, $msDelay)
    {
        $queryResult = $this->getLastForTrigger(
            $triggerId,
            $serverId
        );
        if (! $queryResult) {
            return true;
        }
        $timeOfLastFiredUpTrigger = $queryResult[0]['created'];
        $timeDiff = $timeOfLastFiredUpTrigger - time();
        return ($this->notificationDelayInHours * $msDelay + $timeDiff >= 0) ? false : true;
    }
}
