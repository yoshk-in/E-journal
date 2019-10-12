<?php


namespace App\CLI\render\event;


use App\base\AbstractRequest;
use App\CLI\render\Format;

abstract class AbstractEventRender
{
    protected $title;
    protected $formatter;
    protected $output = '';
    protected $mainTitle = Format::PRODUCT_NAME . PHP_EOL . PHP_EOL;


    public function render($reporter)
    {
        $this->doRender($reporter);
    }

    public function flush(AbstractRequest $request)
    {
        echo $this->title . PHP_EOL . PHP_EOL;
        printf($this->mainTitle, $request->getProduct());
        echo $this->output;
        $this->output = '';
    }

    abstract protected function doRender($reporter);

}