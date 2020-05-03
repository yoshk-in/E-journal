<?php


namespace App\CLI\render\infoConstructor;


use App\base\AbstractRequest;
use App\CLI\render\Format;
use App\CLI\render\IRender;

abstract class AbstractInfoConstructor implements Format
{
    protected string $title;
    protected IRender $formatter;
    protected string $output = '';
    protected string $mainTitle = Format::PRODUCT_NAME . PHP_EOL . PHP_EOL;
    private AbstractRequest $request;
    protected array $observerBuffer = [];

    public function __construct(AbstractRequest $request)
    {
        $this->request = $request;
        $this->initFormatter();
    }

    public function handle($reporter)
    {
        $this->observerBuffer[] = $reporter;
        $this->doRender($reporter);
    }

    public function getObserverBuffer(): array
    {
        return $this->observerBuffer;
    }

    public function getOutput()
    {
        return $this->title . PHP_EOL . PHP_EOL .
            sprintf($this->mainTitle, $this->request->getProductName()) .
            $this->output . PHP_EOL;
    }

    abstract protected function doRender($reporter);

    abstract protected function initFormatter(): void;

}