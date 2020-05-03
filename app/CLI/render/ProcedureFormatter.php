<?php


namespace App\CLI\render;


use App\domain\procedures\CasualProcedure;
use App\domain\procedures\traits\IProductProcedure;

class ProcedureFormatter implements IRender
{
    const TIME = Format::TIME;
    const NONE = Format::NONE;
    const FULL = Format::FULL;

    protected string $pattern;

    public function __construct(string $formatPattern = self::FULL)
    {
        $this->pattern = $formatPattern;
    }

    public function handle(CasualProcedure $processed): string
    {
        return sprintf($this->pattern, $processed->getName(), $this->getStart($processed), $this->getEnd($processed));
    }

    protected function getEnd(CasualProcedure $proc): string
    {
        return $proc->getEnd() ? $proc->getEnd()->format(self::TIME) : self::NONE;
    }

    protected function getStart(CasualProcedure $proc): string
    {
        return $proc->getStart()? $proc->getStart()->format(self::TIME) : self::NONE;
    }

}