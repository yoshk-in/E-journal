<?php


namespace App\CLI\render;


class ShortProcedureFormatter extends ProcedureFormatter
{
    public function __construct(string $formatPattern = Format::SHORT)
    {
        parent::__construct($formatPattern);
    }

    public function handle($processed): string
    {
        return sprintf($this->pattern, $processed->getName(), $this->getEnd($processed));
    }
}