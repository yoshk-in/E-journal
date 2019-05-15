<?php

namespace App\domaini;


use App\base\exceptions\AppException;

class TechProcedure extends Procedure
{

    public function getStartProc() : \DateTime
    {
        return $this->startProcedure;
    }

    public function setStartProc(): void
    {
        if (is_null($this->interval)) {
            throw new AppException('prop interval is required');
        }
        $this->startProcedure = new \DateTime('now');
        $this->endProcedure = (clone $this->startProcedure)->add($this->interval);
    }

    public function setEndProcedure(): void
    {
        throw new AppException('tt procedure end is not selectable');
    }


    public function getEndProcess() : \DateTime
    {
        return $this->endProcedure;
    }

    public function isFinished() : bool
    {
        if ((new \DateTime('now')) > $this->endProcedure) {
            return true;
        }
        return false;
    }

    public function getInterval(): \DateInterval
    {
        return $this->interval;
    }

}

