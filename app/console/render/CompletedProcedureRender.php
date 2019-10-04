<?php


namespace App\console\render;


use App\domain\AbstractProcedure;

class CompletedProcedureRender
{
    protected $pattern = Format::FULL_INFO;

    public function render(AbstractProcedure $procedure)
    {
        $output = sprintf(
            $this->pattern,
            $procedure->getName(),
            $procedure->getStart()->format(Format::TIME),
            $procedure->getEnd()->format(Format::TIME)
        );

        return $output;
    }

}