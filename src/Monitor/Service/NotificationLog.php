<?php
namespace Monitor\Service;

use Monitor\Model\NotificationLog as NotificationLogModel;

class NotificationLog
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function save(NotificationLogModel $notificationLog)
    {
        $this->em->persist($notificationLog);
        $this->em->flush();
    }

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
}