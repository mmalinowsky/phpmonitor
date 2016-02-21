<?php
namespace Monitor\Service;

use Monitor\Model\ServerHistory as ServerHistoryModel;

class ServerHistory
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function save(ServerHistoryModel $serverHistory)
    {
        $this->em->persist($serverHistory);
        $this->em->flush();
    }

    public function deleteRecordsByTime($time)
    {
        $query = $this->em
            ->createQuery('DELETE from \Monitor\Model\ServerHistory s where s.time < ?1');
        $query->setParameter('1', $time);
        $query->execute();
    }
}