<?php


namespace App\console\render\event;



use App\console\render\FormatCfg;


class FullInfoRender extends AbstractEventRender
{
    protected $title = 'текущая статистика:';

    protected function doRender($products)
    {
        $this->formatter->setFormatForProducts(
            FormatCfg::FULL,
            FormatCfg::FULL_COMP,
            FormatCfg::SHORT_PART
        );
        $this->output .= $this->formatter->formatProducts($products);
        $this->formatter->clear();
        $this->output .= $this->formatter->formatStat($products);
    }


}