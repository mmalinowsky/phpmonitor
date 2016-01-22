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
    }
    
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    private function checkServer($server)
    {
        $this->client->setQuery(
                $server['url_path'],
                ['ping_host' => $server['ping_hostname']]
            );

            $serverData = $this->getServerData(); 
            $serverData['server_id'] = $server['id'];
            $serverData['hostname'] = $server['name'];

            if ($serverData['status'] !== 'online') {
                $serverData['status'] = 'offline';
            }
            $this->notificationFacade->checkTriggers($serverData);
            return $serverData;
    }

    public function run()
    {
        $this->isClientValid();
        $serversArr = array_map([$this, "checkServer"], $this->servers);
        $this->database->addServerHistory($serversArr);
        $this->deleteOldHistoryRecords();
    }

    private function isClientValid()
    {
        if( ! $this->client) {
            throw new \Exception('Client is not valid');
        }
    }

    private function deleteOldHistoryRecords()
    {
        $expireTime = $this->config->get('history_expire_time_in_days');
        $this->database->deleteOldRecords($expireTime * DAY_IN_MS);
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

        if ( ! $resources) {
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
