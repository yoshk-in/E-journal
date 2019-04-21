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

    protected $finished_statements = array();

    /** @Column(type="integer") */
    protected $current_statement;

    protected $statesArray = [
        'writeInBD',
        'prozvon',
        'nastroy',
        'vibro',
        'progon',
        'moroz',
        'jara',
        'mechanikaOTK',
        'electrikaOTK',
        'mechanikaPZ',
        'electrikaPZ',
        'sklad'
    ];


    public function __construct(int $number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setStatement($statement)
    {
        $this->isValidState($statement);
        $this->current_statement[$statement] = new ProductStatement($statement);
    }

    protected function assignStatement()
    {
        return;
    }

    public function getStatement($statement)
    {
        return $this->current_statement;
    }

    public function prozvon()
    {
        $operation = $this->statesArray[1];
        $this->setStatement($operation);
    }

    protected function isValidState($statement)
    {
        if (array_search($statement, $this->statesArray)) return true;
        throw new \App\base\AppException('неправильный тип испытаний');
    }


}
