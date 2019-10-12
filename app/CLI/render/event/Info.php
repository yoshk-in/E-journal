<?php


namespace App\CLI\render\event;


use App\CLI\render\CasualFormatter;
use App\CLI\render\CompositeFormatter;
use App\CLI\render\Format;
use App\CLI\render\PartialFormatter;
use App\CLI\render\CollFormatter;
use App\CLI\render\ProcFormatter;
use App\CLI\render\ProductFormatter;
use App\CLI\render\ProductStat;


class Info extends AbstractEventRender
{
    protected $title = 'текущая статистика:';

//    protected function doRender($products)
//    {
//        $this->formatter->setFormatForProducts(
//            FormatCfg::FULL,
//            FormatCfg::FULL_COMP,
//            FormatCfg::SHORT_PART
//        );
//        $this->output .= $this->formatter->formatProducts($products);
//        $this->formatter->clear();
//        $this->output .= $this->formatter->formatStat($products);
//    }

    protected function doRender($products)
    {
        $this->output =
            (new CollFormatter(Format::EOL))->setForEachFormatter(
                (new ProductFormatter())->setNextHandler(
                    (new CollFormatter(Format::EOL))->setForEachFormatter(
                        (new ProcFormatter())->setFormatters(
                            new CasualFormatter(),
                            new CompositeFormatter(),
                            (new CollFormatter(Format::COMMA))->setForEachFormatter(new PartialFormatter())
                        )
                    )
                )
            )->handle($products);
        $this->output .= (new ProductStat())->get($products);
    }


}