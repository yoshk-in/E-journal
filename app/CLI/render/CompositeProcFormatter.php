<?php


namespace App\CLI\render;


class CompositeProcFormatter extends ProcedureFormatter
{
    private $pattern = Format::COMPOSITE;


    public function handle($processed): string
    {
        return sprintf($this->pattern, $processed->getName(), $this->getStart($processed), $this->getEnd($processed));
    }
}