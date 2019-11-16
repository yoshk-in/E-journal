<?php


namespace App\CLI\render\event;


use App\CLI\render\CasualProcFormatter;
use App\CLI\render\CollFormatter;
use App\CLI\render\CompositeProcFormatter;
use App\CLI\render\Format;
use App\CLI\render\PartialProcFormatter;
use App\CLI\render\ProcFormatter;
use App\CLI\render\ProductFormatter;
use App\CLI\render\ProductStat;


class Info extends AbstractInfoDispatcher
{
    protected $title = 'текущая статистика:';
    protected $statBuffer = [];


    protected function doRender($product)
    {
        $this->output .= $this->formatter->handle($product) . Format::EOL;
        $this->statBuffer[] = $product;
    }

    protected function initFormatter(): void
    {
        if (is_null($this->formatter)) {

            $this->formatter =
                (new ProductFormatter())->setNextHandler(
                    (new CollFormatter(Format::EOL))->setForEachFormatter(
                        (new ProcFormatter())->setFormatters(
                            new CasualProcFormatter(),
                            new CompositeProcFormatter(),
                            (new CollFormatter(Format::COMMA))->setForEachFormatter(new PartialProcFormatter())
                        )
                    )
                );


        }
    }

    public function flush()
    {
        parent::flush();
        echo (new ProductStat())->renderStat($this->statBuffer);
    }
}