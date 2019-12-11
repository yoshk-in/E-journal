<?php


namespace App\GUI\handlers;


use App\CLI\render\ProductStat;
use App\domain\ProductMonthlyCounter;
use App\GUI\components\Dashboard;
use App\GUI\domainBridge\RequestManager;
use App\GUI\scheduler\Scheduler;

class CounterGuiStat extends GuiStat
{
    protected $inputNumber;
    protected $productCounter;
    protected $productNumberL;
    private $events = [];

    public function __construct(ProductStat $stat, Scheduler $scheduler, RequestManager $requestMng, ProductMonthlyCounter $productCounter)
    {
        parent::__construct($stat, $scheduler, $requestMng);
        $this->productCounter = $productCounter;
    }

    public function createStat(Dashboard $dashboard)
    {
        parent::createStat($dashboard);
        [$this->inputNumber, $this->productNumberL] = $dashboard->createNumberCounterLayer();
        $this->updateCounter();
        $this->inputNumber->on('change', \Closure::fromCallable([$this,'changeMonthlyCounterByInput']));
    }

    protected function changeMonthlyCounterByInput()
    {
        $this->productCounter->changeMonthlyCount($this->requestMng->getProduct(), $this->inputNumber->getValue());
    }


    protected function updateCounter()
    {
        $product = $this->requestMng->getProduct();
        $this->inputNumber->setValue($this->productCounter->getMonthlyCount($product));
        $this->productNumberL->setText('последний N ' . $this->productCounter->getLastProductNumber($product));
    }

    public function subscribeOn(): array
    {
        $this->events = self::EVENTS;
        $this->events[] = $this->requestMng->getProduct() . self::PRODUCT_START_EVENT;
        return $this->events;
    }

    public function update( $observable, string $event): bool
    {
        if (parent::update($observable, $event)) return true;
        if ($event = $this->requestMng->getProduct() . self::PRODUCT_START_EVENT) {
            $this->updateCounter();
            return true;
        }
    }
}