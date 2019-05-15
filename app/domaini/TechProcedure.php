<?php

namespace App\domaini;

use App\base\exceptions\WrongModelException;

class TechProcedure extends Procedure
{

    public function getStartProc() : \DateTime
    {
        return $this->startProcedure;
    }

    public function setStartProc(): void
    {
        if (is_null($this->interval)) {
            throw new WrongModelException('prop interval is required');
        }
        $this->startProcedure = new \DateTime('now');
        $this->endProcedure = (clone $this->startProcedure)->add($this->interval);
    }

    public function setEndProcedure(): void
    {
        throw new WrongModelException('tt procedure end is not selectable');
    }


    public function getEndProcess() : \DateTime
    {
        return $this->endProcedure;
    }

    public function getInterval(): \DateInterval
    {
        return $this->interval;
    }

    public function isFinished() : bool
    {
        $now_time = new \DateTime('now');
        if ($now_time < $this->endProcedure) {
            return true;
        }
        return false;
    }

}

