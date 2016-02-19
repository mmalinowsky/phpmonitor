<?php
namespace Monitor\Model;

/**
 * @Entity @Table(name="servers_history")
 **/
class ServerHistory
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;
    /**
     * @Column(type="integer")
     **/
    private $server_id;
    /**
     * @Column(type="string")
     **/
    private $hostname;
    /**
     * @Column(type="string")
     **/
    private $status;
    /**
     * @Column(type="float")
     **/
    private $sys_load;
    /**
     * @Column(type="integer")
     **/
    private $cpu_cores;
    /**
     * @Column(type="bigint")
     **/
    private $memory_usage;
    /**
     * @Column(type="bigint")
     **/
    private $memory_total;
    /**;
     * @Column(type="bigint")
     **/
    private $memory_free;
    /**
     * @Column(type="bigint")
     **/
    private $disk_free;
    /**
     * @Column(type="bigint")
     **/
    private $disk_total;
    /**
     * @Column(type="bigint")
     **/
    private $disk_usage;
    /**
     * @Column(type="integer")
     **/
    private $ping;
    /**
     * @Column(type="integer")
     **/
    private $mysql_slow_query;
    /**
     * @Column(type="integer")
     **/
    private $mysql_query_avg;
    /**
     * @Column(type="integer")
     **/
    private $memcache_hits;
    /**
     * @Column(type="integer")
     **/
    private $memcache_miss;
    /**
     * @Column(type="integer")
     **/
    private $memcache_get;
    /**
     * @Column(type="integer")
     **/
    private $memcache_cmd;
    /**
     * @Column(type="integer")
     **/
    private $memcache_bytes;
    /**
     * @Column(type="integer")
     **/
    private $memcache_max_bytes;
    /**
     * @Column(type="integer")
     **/
    private $time;

    public function setServerId($id)
    {
        $this->server_id = $id;
    }

    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setSysLoad($sysload)
    {
        $this->sys_load = $sysload;
    }

    public function setCpuCores($cpuCores)
    {
        $this->cpu_cores = $cpuCores;
    }

    public function setMemoryUsage($memoryUsage)
    {
        $this->memory_usage = $memoryUsage;
    }

    public function setMemoryTotal($memoryTotal)
    {
        $this->memory_total = $memoryTotal;
    }

    public function setMemoryFree($memoryFree)
    {
        $this->memory_free = $memoryFree;
    }

    public function setDiskUsage($diskUsage)
    {
        $this->disk_usage = $diskUsage;
    }

    public function setDiskTotal($diskTotal)
    {
        $this->disk_total = $diskTotal;
    }

    public function setDiskFree($diskFree)
    {
        $this->disk_free = $diskFree;
    }

    public function setPing($ping)
    {
        $this->ping = $ping;
    }

    public function setMysqlSlowQuery($msq)
    {
        $this->mysql_slow_query = $msq;
    }

    public function setMysqlQueryAvg($mqv)
    {
        $this->mysql_query_avg = $mqv;
    }

    public function setMemcacheHits($hits)
    {
        $this->memcache_hits = $hits;
    }

    public function setMemcacheMiss($miss)
    {
        $this->memcache_miss = $miss;
    }

    public function setMemcacheGet($get)
    {
        $this->memcache_get = $get;
    }

    public function setMemcacheCmd($cmd)
    {
        $this->memcache_cmd = $cmd;
    }

    public function setMemcacheBytes($bytes)
    {
        $this->memcache_bytes = $bytes;
    }

    public function setMemcacheMaxBytes($maxBytes)
    {
        $this->memcache_max_bytes = $maxBytes;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }
}
