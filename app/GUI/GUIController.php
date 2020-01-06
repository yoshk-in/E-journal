<?php


namespace App\GUI;


use App\domain\ProductMap;
use App\events\Event;
use App\events\EventChannel;
use App\events\ISubscriber;
use App\events\TCasualSubscriber;
use App\GUI\requestHandling\AddCasualProduct;
use App\GUI\requestHandling\AddDoubleNumberProduct;
use App\GUI\requestHandling\ProductTableSync;
use App\GUI\requestHandling\RequestManager;
use App\GUI\factories\GuiFactory;
use App\GUI\helpers\TProductName;
use App\GUI\render\RenderMng;
use Gui\Application;
use Psr\Container\ContainerInterface;
use React\EventLoop\LoopInterface;


class GUIController  implements ISubscriber
{
    use TCasualSubscriber;

    private Application $gui;
    private ProductMap $productMap;
    private ContainerInterface $container;
    private array $windowSizes = [20, 20, 1600, 900];
    private RenderMng $render;
    private RequestManager $requestMng;
    private EventChannel $channel;

    const EVENTS = [
        Event::GUI_PRODUCT_CHANGED
    ];


    public function __construct(ProductMap $productMap, ContainerInterface $container, EventChannel $channel)
    {
        $this->productMap = $productMap;
        $this->container = $container;
        $this->channel = $channel;
    }

    public function run()
    {
        $this->gui = GuiFactory::create(...$this->windowSizes);
        Debug::set($this->gui, $this->container);
        $this->setUpGuiEnvironment();
        $this->gui->on('start', fn () => $this->render->run());
        $this->gui->run();
    }



    public function update($productName, string $event = Event::GUI_PRODUCT_CHANGED)
    {
        if ($productName == $this->requestMng->getProduct()) return;
        $this->requestMng->changeProduct($productName);
        $this->render->createOrChangeTableComposer();
    }


    private function setUpGuiEnvironment()
    {
        $this->container->set(Application::class, $this->gui);
        $this->container->set(LoopInterface::class, $this->gui->getLoop());
        $this->container->get(UserActionMng::class)->changeHandler($this->container->get(DefaultClickHandler::class));
        $this->channel->subscribeArray([$this->container->get(ProductTableSync::class), $this]);
        $this->requestMng = $this->container->get(RequestManager::class);
        $this->requestMng->changeProduct($product = $this->productMap->first());
        $this->render = $this->container->get(RenderMng::class);
    }







}