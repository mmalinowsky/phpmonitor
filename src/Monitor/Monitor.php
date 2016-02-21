<?php
namespace Monitor;

use Monitor\Notification\Facade;
use Monitor\Model\ServerHistory;
use Monitor\Client\ClientInterface;
use Monitor\Format\FormatInterface;
use Monitor\Config\ConfigInterface;
use Monitor\Service\ServerHistory as ServerHistoryService;
use Doctrine\ORM\EntityRepository;
class Monitor
{
    
    private $config;
    private $serversConfig = [];
    private $serverHistoryStruct = [];
    private $notificationFacade;
    private $client;
    private $format;
    private $serverHistoryService;
    private $serverRepository;

    public function __construct(
        ConfigInterface $config,
        Facade $notificationFacade,
        FormatInterface $format,
        EntityRepository $serverRepository,
        ServerHistoryService $serverHistoryService
    ) {
        $this->config = $config;
        $this->format = $format;
        $this->serverRepository = $serverRepository;
        $this->serversConfig = $this->getServersConfig();
        $this->serverHistoryStruct = $this->getServerHistoryStructure();
        $this->notificationFacade = $notificationFacade;
        $this->serverHistoryService = $serverHistoryService;
    }
    
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function run()
    {
        $this->isClientValid();
        array_map([$this, "checkServer"], $this->serversConfig);
        $this->deleteOldHistoryRecords();
    }

    private function checkServer($serverConfig)
    {
        $this->client->setQuery(
            $serverConfig->getUrlPath(),
            [
                'format'    => $this->config->get('format'),
                'ping_host' => $serverConfig->getPingHostname()
            ]
        );

        $serverData = $this->getServerData();
        $serverData['server_id'] = $serverConfig->getId();
        $serverData['hostname'] = $serverConfig->getName();

        if ($serverData['status'] !== 'online') {
            $serverData['status'] = 'offline';
        }
        $this->addServerHistory($serverData);
        $this->notificationFacade->checkTriggers(
            $serverData,
            $this->config->get('ms_in_hour')
        );
    }

    private function isClientValid()
    {
        if (! $this->client) {
            throw new \Exception('Client is not valid');
        }
    }

    private function deleteOldHistoryRecords()
    {
        $expireTimeInMs = $this->config->get('history_expire_time_in_days') * $this->config->get('ms_in_day');
        $expireTime = time() - $expireTimeInMs;
        $this->serverHistoryService->deleteRecordsByTime($expireTime);
    }

    private function getServersConfig()
    {
        $serverConfigs = $this->serverRepository->findAll();
        return $serverConfigs;
    }

    private function getServerHistoryStructure()
    {
        $reflection = new \ReflectionClass(new Model\ServerHistory);
        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            $properties[] = $property->name;
        }
        unset($properties['id']);
        return $properties;
    }
    /**
     * Fill array with concret value when can't find same key in $arrayTofill as in $struct array
     *
     * @param  array $struct
     * @param  array $arrayToFill
     * @param  int   $value
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
                ['status' => 'offline']
            );
        }
        $decodedData = $this->format->convertToArray($resources);
        $serverData = $this->fillArrayWithDefaultValue($this->serverHistoryStruct, $decodedData);
        return $serverData;
    }

    private function addServerHistory($server)
    {
        $serverHistory = new ServerHistory;
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
        $this->serverHistoryService->save($serverHistory);
    }
}
