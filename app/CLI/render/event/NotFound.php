<?php


namespace App\CLI\render\event;


use App\CLI\render\Format;

class NotFound extends AbstractInfoDispatcher
{
    protected $title = ' не найдено информации по данным номерам:';

    protected function doRender($reporter)
    {
        $this->output = implode($reporter, Format::COMMA);
    }

    protected function initFormatter(): void
    {
        // TODO: Implement initFormatter() method.
    }
}