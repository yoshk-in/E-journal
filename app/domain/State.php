<?php


namespace App\domain;

/**
 * @Entity @Table(name="states")
 *
 **/

class State
{
    protected $incoming;
    protected $leaving;
    protected $name;
    protected $product;
    /** @Column(type="integer"  **/
    protected $idState;

    public function __construct($name, DomainObject $product, $idState)
    {
        $this->name = $name;
        $this->product = $product;
        $this->idState = $this->product->getNumber() . $idState;
    }

    /**
     * @return mixed
     */
    public function getIncoming()
    {
        return $this->incoming;
    }

    /**
     * @param mixed $incoming
     */
    public function setIncoming($incoming): void
    {
        $this->incoming = $incoming;
    }

    /**
     * @return mixed
     */
    public function getLeaving()
    {
        return $this->leaving;
    }

    /**
     * @param mixed $leaving
     */
    public function setLeaving($leaving): void
    {
        $this->leaving = $leaving;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }
}