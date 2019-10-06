<?php


namespace App\console\render\event;


use App\console\render\FormatCfg;
use App\console\render\Info;
use App\console\render\ProductInfoFormatter;
use App\base\ConsoleRequest;


abstract class AbstractEventRender implements FormatCfg
{
    protected $title;
    protected $formatter;
    protected $output = '';
    protected $request;
    protected $mainTitle = Info::PRODUCT_NAME . PHP_EOL . PHP_EOL;

    public function __construct(ConsoleRequest $request, ProductInfoFormatter $formatter)
    {
        $this->formatter = $formatter;
        $this->request = $request;
    }


    public function render($reporter)
    {
        $this->output .= $this->doRender($reporter) . PHP_EOL;
        $this->formatter->clear();
    }

    public function flush()
    {
        echo $this->title . PHP_EOL . PHP_EOL;
        printf($this->mainTitle, $this->request->getProductName());
        echo $this->output;
        $this->output = '';
    }

    abstract protected function doRender($reporter);

}