<?php


namespace App\domain;


abstract class StatesList
{
    protected $statesArray;
    protected $procedureList;
    protected $statesList;
    protected $currentState;
    protected $currentStateNumber;
    protected $product;

    abstract public function setCurrentStateNumber($number = 0);

    abstract public function nexState($nameState = null, $timeProcess = null);

    abstract public function getStatesList();
}