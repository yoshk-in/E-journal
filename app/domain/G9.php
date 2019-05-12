<?php

namespace App\domain;


use Doctrine\Common\Collections\ArrayCollection;


/** @Entity @Table(name="g9s") * */
class G9 extends DomainObject
{
    protected static $PROCEDURES = [
        'nastroy',
        'technicalTraining',
        'mechanikaOTK',
        'electrikaOTK',
        'mechanikaPZ',
        'electrikaPZ'
    ];
    protected static $TECHNICAL_PROCEDURES_REGULATIONS = [
        'vibro' => 'PT30M',
        'progon' => 'PT2H',
        'moroz' => 'PT2H',
        'rest' => 'PT2H',
        'jara' => 'PT2H'
    ];

    protected static $PROCEDURES_REGULATIONS = [
        'minProcTime' => 'PT30M'
    ];

    protected $procedureCollection;
    protected $TTCollection;
    protected $currentProcedure;
    protected $compositeProcedure;

    public function __construct($number)
    {
        parent::__construct($number);
        $this->compositeProcedure = 'technicalTraining';
        $this->ensure(array_search($this->compositeProcedure, self::$PROCEDURES) !== false,
            "{$this->compositeProcedure} must be equals 'technicalTraining'" );
    }




}

