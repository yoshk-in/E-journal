<?php


namespace App\GUI;


use App\base\GUIRequest;
use App\controller\Controller;
use App\domain\ProductMap;
use App\events\EventChannel;
use App\GUI\domainBridge\ProductTableSync;
use App\GUI\components\CounterDashboard;
use App\GUI\components\Dashboard;
use App\GUI\domainBridge\RequestManager;
use App\GUI\handlers\CounterGuiStat;
use App\GUI\handlers\GuiStat;
use App\GUI\startMode\RenderMng;
use Gui\Application;
use Psr\Container\ContainerInterface;
use React\EventLoop\LoopInterface;
use App\GUI\factories\GuiFactory;
use App\GUI\tableStructure\ProductTableComposer;


class GUIManager
{
    private $gui;
    private $request;
    private $server;
    private $productMap;
    private $container;
    private $windowSizes = [20, 20, 1600, 900];
    private $productStat;
    private $dashboard;
    private $requestMng;
    private $render;



    public function __construct(ProductMap $productMap, ContainerInterface $container, GUIRequest $request, Controller $server)
    {
        $this->productMap = $productMap;
        $this->container = $container;
        $this->request = $request;
        $this->server = $server;
    }

    public function run()
    {
        $this->gui = GuiFactory::create(...$this->getWindowSizes());
        Debug::set($this->gui, $this->container);
        $this->setUpGuiEnvironment();
        $this->gui->on('start', \Closure::fromCallable([$this->render, 'run']));
        $this->gui->run();
    }


    public function getWindowSizes(): array
    {
        return $this->windowSizes;
    }


    private function setUpGuiEnvironment()
    {
        $this->container->set(Application::class, $this->gui);
        $this->container->set(LoopInterface::class, $this->gui->getLoop());
        $this->requestMng = $this->container->get(RequestManager::class);
        $this->productMap->isCountable($this->requestMng->getProduct()) ? $this->initCountableProductEnv() : $this->initNonCountableProductEnv();
        $this->container->set(Dashboard::class, $this->dashboard);
        $this->container->set(GuiStat::class, $this->productStat);
        $this->setSubscribersToEventChannel();
        $this->render = $this->container->get(RenderMng::class);
    }


    private function initCountableProductEnv()
    {
//        $this->dashboard = $this->container->get(CounterDashboard::class);
        $this->productStat = $this->container->get(CounterGuiStat::class);
    }

    private function initNonCountableProductEnv()
    {
        $this->dashboard = $this->container->get(Dashboard::class);
        $this->productStat = $this->container->get(GuiStat::class);
    }

    private function setSubscribersToEventChannel()
    {
        $this->container->get(EventChannel::class)->subscribeArray([
            $this->container->get(ProductTableSync::class),
            $this->productStat
        ]);
    }


}