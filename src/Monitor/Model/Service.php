<?php
namespace Monitor\Model;

/**
 * @Entity @Table(name="services")
 **/
class Service
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
    private $dbcolumns;
    /**
     * @Column(type="boolean")
     **/
    private $percentages;
    /**
     * @Column(type="boolean")
     **/
    private $resize;
    /**
     * @Column(type="boolean")
     **/
    private $show_graph;
    /**
     * @Column(type="boolean")
     **/
    private $show_numbers;

    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getDBColumns()
    {
        return $this->dbcolumns;
    }

    public function getResize()
    {
        return $this->resize;
    }

    public function getShowGraph()
    {
        return $this->show_graph;
    }

    public function getShowNumbers()
    {
        return $this->show_numbers;
    }
}
