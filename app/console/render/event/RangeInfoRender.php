<?php


namespace App\console\render\event;


use App\console\render\FormatCfg;

class RangeInfoRender extends AbstractEventRender
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