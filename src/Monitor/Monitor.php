<?php
namespace Monitor;

use Monitor\Notification\Facade;
use Monitor\Model\Server;
use Monitor\Model\ServerHistory;
use Monitor\Contract\Client\ClientInterface;
use Monitor\Contract\Format\FormatInterface;
use Monitor\Contract\Config\ConfigInterface;
use Monitor\Service\ServerHistory as ServerHistoryService;
use Monitor\Utils\ArrayHelper;
use Doctrine\ORM\EntityRepository;

class Monitor
{

    /**
     * Config
     * @var \Monitor\Contract\Config\ConfigInterface
     */
    private $config;
    /**
     * Servers config consist \Monitor\Model\Server
     * @var array
     */
    private $serversConfig = [];
    /**
     * ServersHistory table structure
     * @var array
     */
    private $serverHistoryStruct = [];
    /**
     * Notification Facade
     * @var \Monitor\Notification\Facade
     */
    private $notificationFacade;
    /**
     * Monitor client
     * @var \Monitor\Contract\Client\ClientInterface
     */
    private $client;
    /**
     * Format
     * @var \Monitor\Contract\Format\FormatInterface
     */
    private $format;
    /**
     * Server history service
     * @var \Monitor\Service\ServerHistory
     */
    private $serverHistoryService;
    /**
     * @var \Monitor\Utils\ArrayHelper
     */
    private $arrayHelper;

    public function __construct(
        ConfigInterface $config,
        Facade $notificationFacade,
        FormatInterface $format,
        array $serversConfig,
        ServerHistoryService $serverHistoryService,
        ArrayHelper $arrayHelper
    ) {
        $this->config = $config;
        $this->format = $format;
        $this->serversConfig = $serversConfig;
        $this->notificationFacade = $notificationFacade;
        $this->serverHistoryService = $serverHistoryService;
        $this->serverHistoryStruct = $this->serverHistoryService->getTableStructure();
        $this->arrayHelper = $arrayHelper;
    }
    
    /**
     * Set client
     *
     * @param \Monitor\Contract\Client\ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Run Monitor
     */
    public function run()
    {
        $this->isClientValid();
        array_map([$this, "checkServer"], $this->serversConfig);
        $this->deleteOldHistoryRecords();
    }

    /**
     * Check server
     *
     * @param \Monitor\Model\Server $serverConfig
     */
    private function checkServer(Server $serverConfig)
    {
        $this->client->setQuery(
            [
                $serverConfig->getUrlPath().$this->config->get('api_request_name'),
                'format'    => $this->config->get('format'),
                'ping_host' => $serverConfig->getPingHostname()
            ]
        );

        $serverData = $this->getServerData();
        $serverData['server_id'] = $serverConfig->getId();
        $serverData['hostname']  = $serverConfig->getName();

        if ($serverData['status'] !== 'online') {
            $serverData['status'] = 'offline';
        }
        $this->serverHistoryService->addServerHistory($serverData);
        $this->notificationFacade->checkTriggers(
            $serverData,
            $this->config->get('ms_in_hour')
        );
    }

    /**
     * Check if client is valid
     *
     * @throws \Exception
     */
    private function isClientValid()
    {
        if (! $this->client) {
            throw new \Exception('Client is not valid');
        }
    }

    /**
     * Delete old server history from DB
     */
    private function deleteOldHistoryRecords()
    {
        $expireTimeInDays = $this->config->get('history_expire_time_in_days');
        $msInDay = $this->config->get('ms_in_day');
        $expireTimeInMs = $expireTimeInDays * $msInDay;
        $expireTime = time() - $expireTimeInMs;
        $this->serverHistoryService->deleteRecordsByTime($expireTime);
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
        $serverData = $this->arrayHelper->fillWithDefaultValue(
            $this->serverHistoryStruct,
            $decodedData
        );
        return $serverData;
    }
}
