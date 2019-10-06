<?php


namespace App\console\render\event;


class BlocksMoveRender extends AbstractEventRender
{
    protected $title = 'Отмечены следующие события';

    protected function doRender($procedure)
    {
       $this->output .= $this->formatter->formatProcedure($procedure);
    }
}