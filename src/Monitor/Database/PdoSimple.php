<?php
namespace Monitor\Database;

use \PDO;

class PdoSimple implements DatabaseInterface
{
    
    private $link = null;

    public function __construct($config)
    {
        list($hostname, $username, $password, $database, $driver) = $config;
        $this->connect($hostname, $username, $password, $database, $driver);
    }

    public function connect($hostname, $username, $password, $database, $driver)
    {
        try {
            $this->link = new PDO(
                "{$driver}:host={$hostname}; dbname={$database}",
                "{$username}",
                "{$password}"
            );
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new Exception('Cant\'t connect to DB');
        }
    }

    public function getServersConfig()
    {
        $query = 'SELECT * from servers';
        $st = $this->link->prepare($query);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTableStructure()
    {
        $st = $this->link->prepare('DESCRIBE servers_history');
        $st->execute();
        return $st->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getLastTriggerTime($triggerId, $serverId)
    {
        $query = 'SELECT created from notification_logs WHERE trigger_id=:triggerId AND server_id = :serverId ORDER BY created DESC LIMIT 1';
        $st = $this->link->prepare($query);
        $st->bindValue(':triggerId', $triggerId, PDO::PARAM_INT);
        $st->bindValue(':serverId', $serverId, PDO::PARAM_INT);
        $st->execute();
        $obj = $st->fetch(PDO::FETCH_OBJ);
        if ($obj) {
            return $obj->created;
        }
            return 0;
    }
    
    public function logTrigger($trigger)
    {
        $query = "INSERT INTO notification_logs (trigger_id, server_id, message, created) VALUES (:triggerId, :serverId, :message, :time)";
        $st = $this->link->prepare($query);
        $st->bindValue(':triggerId', $trigger['id'], PDO::PARAM_INT);
        $st->bindValue(':serverId', $trigger['serverId'], PDO::PARAM_INT);
        $st->bindValue(':message', $trigger['message'], PDO::PARAM_STR);
        $st->bindValue(':time', time(), PDO::PARAM_INT);
        $st->execute();
    }

    public function deleteOldRecords($expireTime)
    {
        $expireTime = time() - $expireTime;
        $query = 'DELETE from servers_history where time < ?';
        $st = $this->link->prepare($query);
        $st->BindParam(1, $expireTime, PDO::PARAM_INT);
        $st->execute();
    }
}
