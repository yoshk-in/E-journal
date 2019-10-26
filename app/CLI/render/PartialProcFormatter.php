<?php


namespace App\CLI\render;


class PartialProcFormatter extends ProcedureFormatter
{
    private $pattern = Format::SHORT;

    public function handle($processed): string
    {
        return sprintf($this->pattern, $processed->getName(), $this->getEnd($processed));
    }
}