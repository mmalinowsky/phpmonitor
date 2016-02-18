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

    public function __call($name, $args)
    {
        $name = strtolower($name);
        $excludedItems = [''];
        if (preg_match('/^get/', $name)) {
            $propertyName = substr($name, 3);
            return $this->$propertyName;
        }
        if (preg_match('/^set/', $name)) {
            $propertyName = substr($name, 3);
            $this->$propertyName = $args[0];
        }
    }
}
