<?php


namespace App\CLI\render;


use App\domain\AbstractProcedure;

abstract class ProcedureFormatter implements IFormatter
{
    const TIME = Format::TIME;
    const HYPHEN = Format::HYPHEN;

    protected function getEnd(AbstractProcedure $proc): string
    {
        return $proc->getEnd() ? $proc->getEnd()->format(self::TIME) : self::HYPHEN;
    }

    protected function getStart(AbstractProcedure $proc): string
    {
        return $proc->getStart()? $proc->getStart()->format(self::TIME) : self::HYPHEN;
    }

    abstract function handle(AbstractProcedure $processed): string;

}