<?php


namespace App\console\render\event;


use App\console\render\Info;

class NotFoundEventRender extends AbstractEventRender
{
    protected $title = ' не найдено информации по данным номерам:';

    protected function doRender($reporter)
    {
        $this->output = implode($reporter, Info::COMMA);
    }
}