<?php


namespace App\GUI\handlers;


use App\base\AppCmd;
use App\CLI\render\ProductStat;
use App\domain\procedures\ProductMonthlyCounter;
use App\events\IEvent;
use App\events\ISubscriber;
use App\events\TCasualSubscriber;
use App\GUI\components\Dashboard;
use App\GUI\components\Rerender;
use App\GUI\components\traits\IRerenderable;
use App\GUI\requestHandling\RequestManager;
use App\GUI\helpers\TProductName;
use App\GUI\scheduler\Scheduler;

class ProductCounter implements ISubscriber
{
    use TProductName, TCasualSubscriber;

    protected ProductMonthlyCounter $productCounter;
    private array $events = [];
    private RequestManager$requestMng;
    private IRerenderable $output;

    public function __construct(RequestManager $requestMng, ProductMonthlyCounter $productCounter)
    {
        $this->productCounter = $productCounter;
        $this->requestMng = $requestMng;
    }

    public function setOutput($output)
    {
        $this->output = $output;
    }


    public function updateOutput()
    {
        $product = $this->getProductName();
        $this->output->rerender('counterLabel', "кол-во от последней отгрузки\n" .
            'последний номер:         ' . (($this->productCounter->getLastProductNumber($product) ?? ' ? ')
                ));
        $this->output->rerender('counterInput', (int) $this->productCounter->getMonthlyCount($product));
    }

    public function subscribeOn(): array
    {
        return $this->productCounter->subscribeOn();
    }

    public function currentProductIsCountable(): bool
    {
        return $this->productCounter->isCountable($this->getProductName());
    }

    public function changeMonthlyCounter(int $value)
    {
        $this->productCounter->changeMonthlyCount($this->getProductName(), $value);
    }

    public function notify($observable, string $event)
    {
        $this->updateOutput();
    }
}