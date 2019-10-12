<?php


namespace App\CLI\render;


class CasualFormatter extends ProcedureFormatter
{
    private $pattern = Format::FULL;

    public function handle($processed): string
    {
        return sprintf($this->pattern, $processed->getName(), $this->getStart($processed), $this->getEnd($processed));
    }
}