<?php


namespace App\CLI\render;

use App\domain\AbstractProcedure;
use App\domain\CompositeProcedure;
use App\domain\Procedure;

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
            case Procedure::class:
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

    public function setFormatters(CasualFormatter $casual, CompositeFormatter $composite, CollFormatter $collFormatter)
    {
        $this->casualFormatter = $casual;
        $this->compositeFormatter = $composite;
        $this->partCollFormatter = $collFormatter;
        return $this;
    }


}