<?php


namespace App\CLI\render\event;


use App\CLI\render\CasualFormatter;

class Move extends AbstractInfoDispatcher
{
    protected $title = 'Отмечены следующие события';

    protected function doRender($procedure)
    {        
       $this->output .= $this->formatter->handle($procedure) . PHP_EOL;
    }

    protected function initFormatter(): void
    {
        if (is_null($this->formatter)) {
            $this->formatter = new CasualFormatter();
        }
    }
}