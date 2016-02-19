<?php
namespace Monitor\Model;

/**
 * @Entity @Table(name="servers")
 **/
class Server
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;
    /**
     * @Column(type="string")
     **/
    private $name;
    /**
     * @Column(type="string")
     **/
    private $url_path;
    /**
     * @Column(type="string")
     **/
    private $ping_hostname;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrlPath()
    {
        return $this->url_path;
    }

    public function getPingHostname()
    {
        return $this->ping_hostname;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setUrlPath($urlPath)
    {
        $this->url_path = $urlPath;
    }

    public function setPingHostname($pingHostname)
    {
        $this->ping_hostname = $pingHostname;
    }
}
