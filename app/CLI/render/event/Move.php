<?php


namespace App\CLI\render\event;


class Move extends AbstractEventRender
{
    protected $title = 'Отмечены следующие события';

    protected function doRender($procedure)
    {
       $this->output .= $this->formatter->formatProcedure($procedure);
    }
}