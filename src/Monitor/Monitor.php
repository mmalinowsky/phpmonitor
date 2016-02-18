<?php
namespace Monitor;

use Monitor\Database\DatabaseInterface;
use Monitor\Notification\Facade;
use Monitor\Client\ClientInterface;
use Monitor\Format\FormatInterface;
use Monitor\Config\ConfigInterface;
use Doctrine\ORM\EntityManager;

class Monitor
{
    
    private $config;
    private $serversConfig = [];
    private $serverHistoryStruct = [];
    private $database;
    private $notificationFacade;
    private $client;
    private $format;
    private $entityManager;

    public function __construct(
        ConfigInterface $config,
        DatabaseInterface $database,
        Facade $notificationFacade,
        FormatInterface $format,
        EntityManager $entityManager
    ) {
        $this->config = $config;
        $this->database = $database;
        $this->serversConfig = $this->database->getServersConfig();
        $this->serverHistoryStruct = $this->database->getTableStructure();
        $this->notificationFacade = $notificationFacade;
        $this->format = $format;
        $this->entityManager = $entityManager;
    }
    
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    private function checkServer($serverConfig)
    {
        $this->client->setQuery(
            $serverConfig['url_path'],
            [
                'format'    => $this->config->get('format'),
                'ping_host' => $serverConfig['ping_hostname']
            ]
        );

            $serverData = $this->getServerData();
            $serverData['server_id'] = $serverConfig['id'];
            $serverData['hostname'] = $serverConfig['name'];

        if ($serverData['status'] !== 'online') {
            $serverData['status'] = 'offline';
        }
            $this->addServerHistory($serverData);
            $this->notificationFacade->checkTriggers($serverData, $this->config->get('ms_in_hour'));
    }

    public function run()
    {
        $this->isClientValid();
        array_map([$this, "checkServer"], $this->serversConfig);
        $this->deleteOldHistoryRecords();
    }

    private function isClientValid()
    {
        if (! $this->client) {
            throw new \Exception('Client is not valid');
        }
    }

    private function deleteOldHistoryRecords()
    {
        $expireTime = $this->config->get('history_expire_time_in_days');
        $this->database->deleteOldRecords($expireTime * $this->config->get('ms_in_day'));
    }

    /**
     * Fill array with concret value when can't find same key in $arrayTofill as in $struct array
     *
     * @param array $struct
     * @param array $arrayToFill
     * @param int $value
     * @return array $arrayMerged
     */
    private function fillArrayWithDefaultValue(array $struct, array $arrayToFill, $value = 0)
    {
        $arrayDiff = array_diff($struct, $arrayToFill);
        $arrayDiffFilled = array_fill_keys($arrayDiff, $value);
        $arrayMerged = array_merge($arrayDiffFilled, $arrayToFill);
        return $arrayMerged;
    }

    /**
     * Get server data
     *
     * @return array $serverData
     */
    private function getServerData()
    {
        $resources = $this->client->getResources();

        if (! $resources) {
            $resources = json_encode(
                array(
                'status' => 'offline'
                )
            );
        }
        $decodedData = $this->format->convertToArray($resources);
        $serverData = $this->fillArrayWithDefaultValue($this->serverHistoryStruct, $decodedData);
        return $serverData;
    }

    private function addServerHistory($server)
    {
        $serverHistory = new Model\ServerHistory;
        $serverHistory->setServer_id($server['server_id']);
        $serverHistory->setHostname($server['hostname']);
        $serverHistory->setStatus($server['status']);
        $serverHistory->setSys_load($server['sys_load']);
        $serverHistory->setCpu_cores($server['cpu_cores']);
        $serverHistory->setMemory_usage($server['memory_usage']);
        $serverHistory->setMemory_total($server['memory_total']);
        $serverHistory->setMemory_free($server['memory_free']);
        $serverHistory->setDisk_free($server['disk_free']);
        $serverHistory->setDisk_total($server['disk_total']);
        $serverHistory->setDisk_usage($server['disk_usage']);
        $serverHistory->setPing($server['ping']);
        $serverHistory->setMysql_slow_query($server['mysql_slow_query']);
        $serverHistory->setMysql_query_avg($server['mysql_query_avg']);
        $serverHistory->setMemcache_hits($server['memcache_hits']);
        $serverHistory->setMemcache_miss($server['memcache_miss']);
        $serverHistory->setMemcache_get($server['memcache_get']);
        $serverHistory->setMemcache_cmd($server['memcache_cmd']);
        $serverHistory->setMemcache_bytes($server['memcache_bytes']);
        $serverHistory->setMemcache_max_bytes($server['memcache_max_bytes']);
        $serverHistory->setTime(time());
        $this->entityManager->persist($serverHistory);
        $this->entityManager->flush();
    }
}
