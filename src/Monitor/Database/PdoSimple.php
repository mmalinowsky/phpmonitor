<?php
namespace Monitor\Database;

use \PDO;

class PdoSimple implements DatabaseInterface
{
    
    private $link = null;
    private $config;

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

    public function getNotifications()
    {
        $query = "SELECT * FROM notifications";
        $st = $this->link->prepare($query);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServices()
    {
        $query = "SELECT name, sub, percentages, dbcolumns FROM services";
        $st = $this->link->prepare($query);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
    }

    public function getNotificationTriggers()
    {
        $query = 'SELECT * from notification_triggers LEFT JOIN notifications ON notifications.id=notification_triggers.notification_id';
        $st = $this->link->prepare($query);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
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
    
    public function addServerHistory($data)
    {
        $query = "INSERT INTO servers_history (server_id, hostname, status, sys_load, cpu_cores, memory_usage, memory_total, memory_free, disk_free, disk_total, disk_usage, ping, mysql_slow_query, mysql_query_avg, memcache_hits, memcache_miss, memcache_get, memcache_cmd, memcache_bytes, memcache_max_bytes, time)
	VALUES (:server_id, :hostname, :status, :sys_load, :cpu_cores, :memory_usage, :memory_total, :memory_free, :disk_free, :disk_total, :disk_usage, :ping, :mysql_slow_query, 	:mysql_query_avg, :memcache_hits, :memcache_miss, :memcache_get, :memcache_cmd, :memcache_bytes, :memcache_max_bytes, :time)";
        $st = $this->link->prepare($query);

        foreach ($data as $row) {
            $st->bindValue(':server_id', $row['server_id'], PDO::PARAM_INT);
            $st->bindValue(':hostname', $row['hostname'], PDO::PARAM_STR);
            $st->bindValue(':status', $row['status'], PDO::PARAM_STR);
            $st->bindValue(':sys_load', $row['sys_load'], PDO::PARAM_STR);
            $st->bindValue(':cpu_cores', $row['cpu_cores'], PDO::PARAM_INT);
            $st->bindValue(':memory_usage', $row['memory_usage'], PDO::PARAM_INT);
            $st->bindValue(':memory_total', $row['memory_total'], PDO::PARAM_INT);
            $st->bindValue(':memory_free', $row['memory_free'], PDO::PARAM_INT);
            $st->bindValue(':disk_free', $row['disk_free'], PDO::PARAM_INT);
            $st->bindValue(':disk_total', $row['disk_total'], PDO::PARAM_INT);
            $st->bindValue(':disk_usage', $row['disk_usage'], PDO::PARAM_INT);
            $st->bindValue(':ping', $row['ping'], PDO::PARAM_INT);
            $st->bindValue(':mysql_slow_query', $row['mysql_slow_query'], PDO::PARAM_INT);
            $st->bindValue(':mysql_query_avg', $row['mysql_query_avg'], PDO::PARAM_INT);
            $st->bindValue(':memcache_hits', $row['memcache_hits'], PDO::PARAM_INT);
            $st->bindValue(':memcache_miss', $row['memcache_miss'], PDO::PARAM_INT);
            $st->bindValue(':memcache_get', $row['memcache_get'], PDO::PARAM_INT);
            $st->bindValue(':memcache_cmd', $row['memcache_cmd'], PDO::PARAM_INT);
            $st->bindValue(':memcache_bytes', $row['memcache_bytes'], PDO::PARAM_INT);
            $st->bindValue(':memcache_max_bytes', $row['memcache_max_bytes'], PDO::PARAM_INT);
            $st->bindValue(':time', time(), PDO::PARAM_INT);
            $st->execute();
        }
    }
}
