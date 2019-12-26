<?php


namespace App\GUI\handlers;

use App\base\AppMsg;
use App\CLI\render\ProductStat;
use App\events\ISubscriber;
use App\GUI\components\Rerender;
use App\GUI\components\traits\IRerenderable;
use App\GUI\requestHandling\RequestManager;
use App\GUI\helpers\TProductName;
use App\GUI\scheduler\Scheduler;
use App\helpers\AutoGenCollection;
use Psr\Container\ContainerInterface;

class GuiProductStat implements ISubscriber
{
    use TProductName;

    protected ProductStat $currentStat;
    protected ?IRerenderable $output;
    protected string $title = "статистика блоков в работе: \n";
    protected bool $printTaskScheduled = false;
    protected Scheduler$scheduler;
    protected RequestManager $requestMng;

    private AutoGenCollection $statisticColl;

    const EVENTS = [
        AppMsg::GUI_INFO,
        AppMsg::PRODUCT_MOVE,
    ];


    public function __construct(Scheduler $scheduler, RequestManager $requestMng, ContainerInterface $container, Rerender $output)
    {
        $this->scheduler = $scheduler;
        $this->requestMng = $requestMng;

        $statCollProps = AutoGenCollection::getBlank();
        $statCollProps->class = ProductStat::class;
        //handler to getting object from ProductStat Collection
        $statCollProps->get = fn(ProductStat $current) => $this->updateOutput($current);
        $statCollProps->make = $statCollProps->get;
        $this->statisticColl = new AutoGenCollection($container, $statCollProps);
        $this->output = $output;
        $this->statisticColl->gen($this->getProductName());
    }




    public function updateStat()
    {
        $this->printTaskScheduled = true;
        $this->scheduler->asyncFutureTick(function () {
            $this->updateOutput($this->currentStat);
            $this->currentStat->resetOutput();
            $this->printTaskScheduled = false;
        });
    }

    public function update($product, string $event)
    {
        if ($product->isStarted()) {
            $this->currentStat->computeOne($product);
            $this->printTaskScheduled ?: $this->updateStat();
        }
    }

    public function subscribeOn(): array
    {
        $events = self::EVENTS;
        $events[0] = $this->productInfoEvent();
        $product = $this->requestMng->getProduct();
        $this->currentStat = $this->statisticColl->gen($product);
        return $events;
    }

    public function updateOutput($currentStat)
    {
        $this->currentStat = $currentStat;
        $this->output->rerender('stat', $res = $this->title . $this->currentStat->getComputed());
        $this->currentStat->resetOutput();
    }

    protected function productInfoEvent(): string
    {
        return AppMsg::GUI_INFO . $this->getProductName();
    }


}