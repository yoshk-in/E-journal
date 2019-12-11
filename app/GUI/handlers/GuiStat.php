<?php


namespace App\GUI\handlers;

use App\base\AppMsg;
use App\CLI\render\ProductStat;
use App\domain\Product;
use App\events\ISubscriber;
use App\GUI\components\Dashboard;
use App\GUI\domainBridge\RequestManager;
use App\GUI\scheduler\Scheduler;

class GuiStat implements ISubscriber
{
    protected $stat;
    protected $output;
    protected $title = "текущая статистика: \n";
    protected $printTaskScheduled = false;
    protected $getStatTaskScheduled = false;
    protected $inputNumber;
    protected $scheduler;
    protected $requestMng;

    const EVENTS = [
        AppMsg::GUI_INFO,
        AppMsg::STAT_INFO,
        AppMsg::PRODUCT_MOVE,
    ];

    const PRODUCT_START_EVENT = AppMsg::PRODUCT_STARTED;


    public function __construct(ProductStat $stat, Scheduler $scheduler, RequestManager $requestMng)
    {
        $this->stat = $stat;
        $this->scheduler = $scheduler;
        $this->requestMng = $requestMng;
    }

    public function createStat(Dashboard $dashboard)
    {
        $this->output = $dashboard->createStatLabel();
    }

    public function updateStat()
    {
        $this->printTaskScheduled = true;
        $this->scheduler->asyncFutureTick(\Closure::fromCallable([$this, 'printStat']));
    }


    public function update($observable, string $event): bool
    {
        switch ($event) {
            case ($event === self::EVENTS[0] || $event === self::EVENTS[1]):
                $this->updateProductStat($observable);
                return true;
            case self::EVENTS[2]:
                $this->getAppStat();
                return true;
        }
        return false;
    }

    public function subscribeOn(): array
    {
        return self::EVENTS;
    }

    public function getStatTaskComplete()
    {
        $this->getStatTaskScheduled = false;
    }


    protected function updateProductStat(Product $product)
    {
        if ($product->isStarted()) {
            $this->stat->oneProductStatStep($product);
            $this->printTaskScheduled ?: $this->updateStat();
        }
    }

    protected function printStat()
    {
        $this->output->setText($this->title . $this->stat->getStat());
        $this->stat->resetBuffer();
        $this->printTaskScheduled = false;
    }

    protected function _getAppStat()
    {
        $this->requestMng->statInfo();
        $this->getStatTaskComplete();
    }

    protected function getAppStat()
    {
        if ($this->getStatTaskScheduled) return;
        $this->getStatTaskScheduled = true;
        $this->scheduler->asyncFutureTick(\Closure::fromCallable([$this, '_getAppStat']));

    }
}