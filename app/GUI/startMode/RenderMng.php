<?php


namespace App\GUI\startMode;


use App\domain\CasualNumberStrategy;
use App\domain\CompositeNumberStrategy;
use App\domain\ProductMap;
use App\events\Event;
use App\events\EventChannel;
use App\events\ISubscriber;
use App\GUI\components\Dashboard;
use App\GUI\components\NewDashboard;
use App\GUI\components\Pager;
use App\GUI\domainBridge\RequestManager;
use App\GUI\handlers\CounterGuiStat;
use App\GUI\handlers\GuiStat;
use App\GUI\tableStructure\CompositeNumberProductTableComposer;
use App\GUI\tableStructure\ProductTableComposer;
use DI\Container;


class RenderMng implements ISubscriber
{
    private $tComposers = [];
    private $currentComposer;
    private $dashboard;
    private $requestMng;
    private $channel;
    private $container;
    private $productMap;
    private $tableRender = [
        CasualNumberStrategy::class => ProductTableComposer::class,
        CompositeNumberStrategy::class => CompositeNumberProductTableComposer::class
    ];

    private $stat = [
        GuiStat::class,
        CounterGuiStat::class
    ];

    const EVENTS = [
        Event::GUI_PRODUCT_CHANGED
    ];

    private $newDashboard;


    public function __construct(RequestManager $requestMng, ProductMap $productMap, /*Dashboard $dashboard, */ Container $container, EventChannel $channel, NewDashboard $newDashboard)
    {
        $this->requestMng = $requestMng;
        $this->productMap = $productMap;
//        $this->dashboard = $dashboard;
        $this->container = $container;
        $this->channel = $channel;
        $this->channel->subscribe($this);
        $this->newDashboard = $newDashboard;
    }

    function run()
    {
        $product = $this->getProductName();
        $this->makeTableComposer($product);
        $this->newDashboard->create();
//        $this->dashboard->setStatAnalytic($this->getStatAnalytic($product));
//        $this->dashboard->create();
//        $this->currentComposer->prepareTable($this->requestMng->getProduct());
//        $this->dashboard->create();
//        $this->requestMng->firstRequest();
    }


    public function update($observable, string $event)
    {
        $product = $this->getProductName();
        $this->currentComposer->setVisible(false);
        isset($this->tComposers[$product]) ?
            $this->changeVisibleProductComposer($product)
            :
            $this->makeTableComposer($product);
    }

    public function subscribeOn(): array
    {
        return self::EVENTS;
    }

    private function makeTableComposer(string $product)
    {
        $this->currentComposer = $this->tComposers[$product] = $this->container->make(
            $this->getTableComposer($product),
            [
                'product' => $product,
                'pager' => $this->container->make(Pager::class),
            ]
        );
        $this->channel->subscribe($this->currentComposer);
        $this->currentComposer->prepareTable($product);
        $this->requestMng->firstRequest();
    }

    private function getTableComposer(string $product): string
    {
        return $this->tableRender[$this->productMap->getNumberStrategy($product)];
    }

    private function changeVisibleProductComposer(string $product)
    {
        $this->currentComposer = $this->tComposers[$product];
        $this->currentComposer->setVisible(true);
    }

    private function getProductName(): string
    {
        return $this->requestMng->getProduct();
    }

    private function getStatAnalytic(string $product): GuiStat
    {
        return $this->productMap->isCountable($product) ? $this->container->get($this->stat[1]) : $this->get($this->stat[0]);
    }
}