<?php


namespace App\CLI\render\event;


use App\CLI\render\FormatCfg;

class RangeInfo extends AbstractEventRender
{
    protected $title = 'найдена следующая информация:';

    protected function doRender($reporter)
    {
        $this->formatter->setFormatForProducts(
            FormatCfg::FULL,
            FormatCfg::FULL,
            FormatCfg::FULL
        );
        $this->output .= $this->formatter->formatProducts($reporter);
    }
}