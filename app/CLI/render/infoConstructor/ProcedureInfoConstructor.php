<?php


namespace App\CLI\render\infoConstructor;


use App\CLI\render\ProcedureFormatter;

class ProcedureInfoConstructor extends AbstractInfoConstructor
{
    protected string $title = 'Отмечены следующие события';

    protected function doRender($procedure)
    {
        $this->output .= $this->formatter->handle($procedure) . PHP_EOL;
    }

    protected function initFormatter(): void
    {
        $this->formatter = new ProcedureFormatter();
    }
}