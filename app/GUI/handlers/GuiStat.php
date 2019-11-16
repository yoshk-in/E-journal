<?php


namespace App\GUI\handlers;

use App\base\AppMsg;
use App\CLI\render\ProductStat;
use App\domain\Product;
use App\events\Event;
use App\events\ISubscriber;
use App\GUI\components\LabelWrapper;
use App\GUI\GUIManager;
use React\EventLoop\LoopInterface;

class GuiStat implements ISubscriber
{
    private $stat;
    private $output;
    private $title = "текущая статистика: \n";
    private $loop;
    private $printTaskScheduled = false;
    private $getStatTaskScheduled = false;

    private $app;

    const EVENTS = [
        AppMsg::GUI_INFO,
        AppMsg::STAT_INFO,
        AppMsg::PRODUCT_MOVE,
    ];


    public function __construct(ProductStat $stat, LoopInterface $loop, GUIManager $app)
    {
        $this->stat = $stat;
        $this->loop = $loop;
        $this->app = $app;
    }

    public function attachOutput(LabelWrapper $object)
    {
        $this->output = $object;
    }

    public function updateStat()
    {
        $this->printTaskScheduled = true;
        $this->loop->futureTick(function () {
            $this->output->setText($this->title . $this->stat->getStat());
            $this->stat->resetBuffer();
            $this->printTaskScheduled = false;
        });
    }


    public function update(Object $observable, string $event)
    {
        switch ($event) {
            case ($event === self::EVENTS[0] || $event === self::EVENTS[1]):
                $this->updateProductStat($observable);
                break;
            case self::EVENTS[2]:
                $this->getAppStat();
        }
    }

    public function subscribeOn(): array
    {
        return self::EVENTS;
    }

    private function updateProductStat(Product $product)
    {
        if ($product->isStarted()) {
            $this->stat->oneProductStatStep($product);
            $this->printTaskScheduled ?: $this->updateStat();
        }
    }

    private function getAppStat()
    {
        if ($this->getStatTaskScheduled) return;
        $this->getStatTaskScheduled = true;
        $this->loop->futureTick(function () {
            $this->app->statRequest();
            $this->getStatTaskScheduled = false;
        });

    }
}