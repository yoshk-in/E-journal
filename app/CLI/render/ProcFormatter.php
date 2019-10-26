<?php


namespace App\CLI\render;

use App\domain\AbstractProcedure;
use App\domain\CompositeProcedure;
use App\domain\CasualProcedure;

class ProcFormatter extends Formatter
{
    private $compositeFormatter;
    private $casualFormatter;
    private $partCollFormatter;


    final function handle($processed): string
    {
        ($processed instanceOf AbstractProcedure) || $this->exception();
        $buffer = '';
        switch (get_class($processed)) {
            case CasualProcedure::class:
                $buffer .= $this->casualFormatter->handle($processed);
                break;
            case CompositeProcedure::class:
                $buffer .= $this->compositeFormatter->handle($processed);
                $buffer .= $this->partCollFormatter->handle($processed->getInners());
        }
        return $buffer;
    }

    final protected function doHandle($processed): void
    {
    }

    public function setFormatters(CasualProcFormatter $casual, CompositeProcFormatter $composite, CollFormatter $collFormatter)
    {
        $this->casualFormatter = $casual;
        $this->compositeFormatter = $composite;
        $this->partCollFormatter = $collFormatter;
        return $this;
    }


}