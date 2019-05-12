<?php

namespace App\domaini;


use App\base\AppException;

class TechnicalProcedure extends Procedure
{

    public function getStart() : \DateTime
    {
        return $this->start;
    }

    public function setStart(): void
    {
        if (is_null($this->interval)) throw new AppException('prop interval is required');
        $this->start = new \DateTime('now');
        $this->end = (clone $this->start)->add($this->interval);
    }

    public function setEnd(): void
    {
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

    public function getInterval(): \DateInterval
    {
        return $this->procedureTime;
    }

}

