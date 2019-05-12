<?php

namespace App\domain;


use App\base\AppException;

class TechnicalProcedure extends Procedure
{
    private $procedureTime;

    public function __construct(string $name, DomainObject $product, int $idStage, $time)
    {
        $this->name = $name;
        $this->product = $product;
        $this->idStage = $idStage;
        $this->setProcedureTime($time);
        $this->setStartProcess();
    }


    public function setProcedureTime($time): void
    {
        $this->procedureTime = new \DateInterval($time);
    }


    public function getStartProcess() : \DateTime
    {
        return $this->start;
    }

    public function setStartProcess(): void
    {
        $this->start = new \DateTime('now');
        $this->end = (clone $this->start)->add($this->procedureTime);
    }


    public function getEndProcess() : \DateTime
    {
        return $this->end;
    }

    public function isFinished() : bool
    {
        if ((new \DateTime('now')) > $this->end) return true;
        return false;
    }

    public function getMinTime(): \DateInterval
    {
        return $this->procedureTime;
    }

}

