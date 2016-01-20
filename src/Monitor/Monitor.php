<?php
namespace Monitor;

use Monitor\Database\DatabaseInterface;
use Monitor\Notification\Facade;
use Monitor\Client\ClientInterface;

class Monitor
{
    
    private $config;
    private $servers = [];
    private $serverHistoryStruct = [];
    private $database;
    private $notificationFacade;
    private $client;

    public function __construct(Config $config, DatabaseInterface $database, Facade $notificationFacade)
    {
        $this->config = $config;
        $this->database = $database;
        $this->servers = $this->database->getServersConfig();
        $this->serverHistoryStruct = $this->database->getTableStructure();
        $this->notificationFacade = $notificationFacade;
        DEFINE('HOUR_IN_MS', 3600);
        DEFINE('DAY_IN_MS', HOUR_IN_MS * 24);
    }
    
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function run()
    {
        foreach ($this->servers as $key => $value) {
            $this->client->setQuery(
                $value['url_path'],
                ['ping_host' => $value['ping_hostname']]
            );

            $this->servers[$key] = $this->getServerData();
            $this->servers[$key]['server_id'] = $value['id'];
            $this->servers[$key]['hostname'] = $value['name'];

            if ($this->servers[$key]['status'] !== 'online') {
                $this->servers[$key]['status'] = 'offline';
            }
            
            $this->notificationFacade->checkTriggers($this->servers[$key]);
        }

        $this->database->addServerHistory($this->servers);
        $this->deleteOldHistoryRecords();
    }

    private function deleteOldHistoryRecords()
    {
        $this->database->deleteOldRecords($this->config->get('history_expire_time_in_days') * DAY_IN_MS);
    }

    /**
     * Fill array with concret value
     *
     * @param array $struct
     * @param array $data
     * @param int $value
     * @return array $data
     */
    private function fillArrayWithDefaultValue(array $struct, array $data, $value = 0)
    {
        foreach ($struct as $row) {
            if (!isset($data[$row])) {
                $data[$row] = $value;
            }
        }
        return $data;
    }
    
    /**
     * Decode resources to array
     *
     * @param string $undecodedData
     * @return array $data
     */
    private function decodeData($undecodedData)
    {
        $data = json_decode($undecodedData, true);
        if (!is_array($data)) {
            $data = array();
        }
        return $data;
    }

    /**
     * Get server data
     *
     * @return array $serverData
     */
    private function getServerData()
    {
        $resources = $this->client->getResources();

        if (!$resources) {
            $resources = json_encode(
                array(
                'status' => 'offline'
                )
            );
        }
        $decodedData = $this->decodeData($resources);
        $serverData = $this->fillArrayWithDefaultValue($this->serverHistoryStruct, $decodedData);
        return $serverData;
    }
}
