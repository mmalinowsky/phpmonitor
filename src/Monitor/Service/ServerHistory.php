<?php
namespace Monitor\Service;

use Monitor\Model\ServerHistory as ServerHistoryModel;
use Doctrine\ORM\EntityManager;

class ServerHistory
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Save server history
     *
     * @param \Monitor\Model\ServerHistory $serverHistory
     */
    public function save(ServerHistoryModel $serverHistory)
    {
        $this->em->persist($serverHistory);
        $this->em->flush();
    }

    /**
     * Delete old server history records by specifing time
     *
     * @param int $time
     */
    public function deleteRecordsByTime($time)
    {
        $query = $this->em
            ->createQuery('DELETE from \Monitor\Model\ServerHistory s where s.time < ?1');
        $query->setParameter('1', $time);
        $query->execute();
    }
}
