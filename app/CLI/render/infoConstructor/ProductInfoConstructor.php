<?php


namespace App\CLI\render\infoConstructor;


use App\CLI\render\CollFormatter;
use App\CLI\render\Format;
use App\CLI\render\ProcedureFormatter;
use App\CLI\render\ShortProcedureFormatter;
use App\CLI\render\ProcedureRender;
use App\CLI\render\ProductFormatter;
use App\base\AbstractRequest;


class ProductInfoConstructor extends AbstractInfoConstructor
{
    protected string $title = 'найдена следующая информация:';

    public function __construct(AbstractRequest $request)
    {
        parent::__construct($request);
    }

    protected function doRender($product)
    {
        $this->output .= $this->formatter->handle($product) . PHP_EOL;
    }

    protected function initFormatter(): void
    {
        $empty_title = '';
        $partials_title = Format::PARTIAL_TITLE;
        $this->formatter =
            (new ProductFormatter())->setNext(
                (new CollFormatter(Format::EOL, $empty_title))
                    ->setForEachFormatter(
                        (new ProcedureRender())->setFormatters(
                            new ProcedureFormatter(),
                            (new CollFormatter(Format::COUNT_DELIMITER, $partials_title))
                                ->setForEachFormatter(new ShortProcedureFormatter())
                        )
                    )
            );
    }


}