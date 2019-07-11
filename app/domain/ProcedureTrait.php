<?php


namespace App\domain;

use App\base\exceptions\IncorrectInputException;
use App\base\exceptions\WrongModelException;
use DateTimeImmutable;



trait ProcedureTrait
{
    /**
     *
     * @Column(type="datetime", nullable=true)
     **/
    protected $startProcedure;
    /**
     *
     * @Column(type="datetime", nullable=true)
     **/
    protected $endProcedure;
    /**
     *
     * @Column(type="string")
     **/
    protected $name;


    protected $idStage;
    /**
     * @Id
     * @Column(type="integer")
     **/
    protected $id;


    public function setEnd(): void
    {
        $this->ensureRighInput(
            (bool)($this->startProcedure),
            'в журнале нет отметки' .
            ' о начале данной процедуры '
        );
        $this->ensureRighInput(
            is_null($this->endProcedure), 'данное событие уже отмечено в журнале'
        );
        $this->endProcedure = new DateTimeImmutable('now');
    }


}