<?php


namespace App\console\event\render;


use App\domain\AbstractProcedure;
use App\console\render\event\AbstractEventRender;
use App\console\render\Format;

class InfoFoundRender extends AbstractEventRender implements Format
{
    protected $title = 'найдена следующая информация:';

    protected function doRender(AbstractProcedure $procedure)
    {
        echo $this->title . PHP_EOL;
        echo $this->formatter->formatProducts() . PHP_EOL;
        echo $this->formatter->formatStat() . PHP_EOL;

    }
}