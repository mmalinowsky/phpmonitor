<?php
namespace Monitor;

use Monitor\Database\DatabaseInterface;
use Monitor\Notification\Facade;
use Monitor\Client\ClientInterface;
use Monitor\Format\FormatInterface;
use Monitor\Config\ConfigInterface;

class Monitor
{
    
    private $config;
    private $serversConfig = [];
    private $serverHistoryStruct = [];
    private $database;
    private $notificationFacade;
    private $client;
    private $format;

    public function __construct(
        ConfigInterface $config,
        DatabaseInterface $database,
        Facade $notificationFacade,
        FormatInterface $format
    ) {
        $this->config = $config;
        $this->database = $database;
        $this->serversConfig = $this->database->getServersConfig();
        $this->serverHistoryStruct = $this->database->getTableStructure();
        $this->notificationFacade = $notificationFacade;
        $this->format = $format;
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
            $this->notificationFacade->checkTriggers($serverData, $this->config->get('ms_in_hour'));
            return $serverData;
    }

    public function run()
    {
        $this->isClientValid();
        $serversData = array_map([$this, "checkServer"], $this->serversConfig);
        $this->database->addServerHistory($serversData);
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
}
