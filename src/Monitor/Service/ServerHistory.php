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

    /**
     * Get 'servers_history' table structure
     *
     * @return array $properties
     */
    public function getTableStructure()
    {
        $reflection = new \ReflectionClass(new \Monitor\Model\ServerHistory);
        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            $properties[] = $property->name;
        }
        unset($properties['id']);
        return $properties;
    }

    /**
     * Store server history
     *
     * @param array $server
     */
    public function addServerHistory(array $server)
    {
        $serverHistory = new ServerHistoryModel;
        $serverHistory->setServerId($server['server_id']);
        $serverHistory->setHostname($server['hostname']);
        $serverHistory->setStatus($server['status']);
        $serverHistory->setSysLoad($server['sys_load']);
        $serverHistory->setCpuCores($server['cpu_cores']);
        $serverHistory->setMemoryUsage($server['memory_usage']);
        $serverHistory->setMemoryTotal($server['memory_total']);
        $serverHistory->setMemoryFree($server['memory_free']);
        $serverHistory->setDiskFree($server['disk_free']);
        $serverHistory->setDiskTotal($server['disk_total']);
        $serverHistory->setDiskUsage($server['disk_usage']);
        $serverHistory->setPing($server['ping']);
        $serverHistory->setMysqlSlowQuery($server['mysql_slow_query']);
        $serverHistory->setMysqlQueryAvg($server['mysql_query_avg']);
        $serverHistory->setMemcacheHits($server['memcache_hits']);
        $serverHistory->setMemcacheMiss($server['memcache_miss']);
        $serverHistory->setMemcacheGet($server['memcache_get']);
        $serverHistory->setMemcacheCmd($server['memcache_cmd']);
        $serverHistory->setMemcacheBytes($server['memcache_bytes']);
        $serverHistory->setMemcacheMaxBytes($server['memcache_max_bytes']);
        $serverHistory->setTime(time());
        $this->save($serverHistory);
    }
}
