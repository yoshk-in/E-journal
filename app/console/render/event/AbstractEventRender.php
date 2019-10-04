<?php


namespace App\console\render\event;


use App\console\render\Formatter;
use App\domain\AbstractProcedure;
use App\events\Event;


abstract class AbstractEventRender implements Event
{
    protected $title;
    protected $formatter;

    public function render(AbstractProcedure $procedure, Formatter $formatter)
    {
        $this->formatter = $formatter;
        $this->doRender($procedure);
    }

    abstract protected function doRender(AbstractProcedure $procRender);

}