<?php

namespace App\domain;

use App\base\exceptions\WrongModelException;
use DateTimeImmutable;
use DateInterval;


trait TechProcedureTrait
{
    use ProcedureTrait;

    protected $interval;

    public function setStart(): void
    {
        parent::setStart();
        if (is_null($this->interval)) {
            throw new WrongModelException('prop interval is required');
        }
        $this->endProcedure =  $this->startProcedure->add($this->interval);
    }


    public function setInterval(string $interval): void
    {
        $this->interval = new DateInterval($interval);
    }

    public function getInterval(): DateInterval
    {
        return $this->interval;
    }

    public function isFinished() : bool
    {
        $now_time = new DateTimeImmutable('now');
        if ($now_time > $this->endProcedure) {
            return true;
        }
        return false;
    }



}

