<?php

namespace App\domain;

//use App\base\AppHelper;

abstract class DomainObject
{
    /**
     *  @Id
     *  @Column(type="integer")
     **/
    protected $number;

    protected $statesList;

    /** @Column(type="integer") */
    protected $currentState;


    public function __construct(int $number)
    {
        $this->number = $number;

    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getStatesList()
    {
        return $this->statesList;
    }

    public function setStatesList(StatesList $statesList): void
    {
        $this->statesList = $statesList;
    }

}
