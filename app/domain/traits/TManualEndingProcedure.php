<?php


namespace App\domain\traits;


use App\domain\Product;

trait TManualEndingProcedure
{



    public function end()
    {
        $this->checkInputCondition((bool)$this->getStart(), ' событие еще не начато');
        $this->checkInputCondition(!$end = $this->getEnd(), ' событие уже отмечено');
        return $this->end = new \DateTimeImmutable('now');
    }
}