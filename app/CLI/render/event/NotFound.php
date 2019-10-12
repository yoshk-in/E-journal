<?php


namespace App\CLI\render\event;


use App\console\render\Info;

class NotFound extends AbstractEventRender
{
    protected $title = ' не найдено информации по данным номерам:';

    protected function doRender($reporter)
    {
        $this->output = implode($reporter, Info::COMMA);
    }
}