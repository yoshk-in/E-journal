<?php


namespace App\CLI\render\event;


use App\base\AbstractRequest;
use App\CLI\render\Format;
use App\CLI\render\IFormatter;

abstract class AbstractInfoDispatcher
{
    protected string $title;
    protected IFormatter $formatter;
    protected string $output = '';
    protected string $mainTitle = Format::PRODUCT_NAME . PHP_EOL . PHP_EOL;
    private AbstractRequest $request;

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