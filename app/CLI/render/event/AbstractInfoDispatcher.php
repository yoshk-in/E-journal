<?php


namespace App\CLI\render\event;


use App\base\AbstractRequest;
use App\CLI\render\Format;

abstract class AbstractInfoDispatcher
{
    protected $title;
    protected $formatter;
    protected $output = '';
    protected $mainTitle = Format::PRODUCT_NAME . PHP_EOL . PHP_EOL;
    private $request;

    public function __construct(AbstractRequest $request)
    {
        $this->request = $request;
    }

    public function handle($reporter)
    {
        $this->initFormatter();
        $this->doRender($reporter);
    }

    public function flush()
    {
        echo $this->title . PHP_EOL . PHP_EOL;
        printf($this->mainTitle, $this->request->getProduct());
        echo $this->output . PHP_EOL;
    }

    abstract protected function doRender($reporter);

    abstract protected function initFormatter(): void;

}