<?php


namespace App\domain\procedures\executionStrategy;


use App\events\Event;
use Doctrine\ORM\Mapping\Entity;

/** @Entity() */
class ManualProcedureExecutionStrategy extends ProcedureExecutionStrategy
{


    public function start( &$startTime, &$endTime)
    {
        $this->checkCurrentState(self::PROCEDURE_READY_TO_START);
        $startTime = new \DateTimeImmutable('now');
    }

    public function end( &$startTime, &$endTime)
    {
        $this->checkCurrentState(self::PROCEDURE_READY_TO_END);
        $endTime = new \DateTimeImmutable('now');
    }

    public function beforeEnd(): ?\DateInterval
    {
        return null;
    }

}