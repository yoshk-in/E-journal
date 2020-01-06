<?php


namespace App\CLI\render;


use App\domain\AbstractProcedure;

class CasualProcFormatter extends ProcedureFormatter
{
    private string $pattern = Format::FULL;

    public function handle(AbstractProcedure $processed): string
    {
        return sprintf($this->pattern, $processed->getName(), $this->getStart($processed), $this->getEnd($processed));
    }
}