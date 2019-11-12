<?php


namespace App\GUI;


use App\base\AppMsg;
use App\base\GUIRequest;
use App\controller\Controller;
use App\domain\ProcedureMap;
use App\events\EventChannel;
use App\events\ProductTableSynchronizer;
use App\GUI\startMode\ModeManager;
use Gui\Components\VisualObjectInterface;
use Psr\Container\ContainerInterface;
use React\EventLoop\LoopInterface;


class GUIManager
{
    private $gui;
    private $request;
    private $server;
    private $procedureMap;
    private $container;
    private $product;
    private $productsPerPage = 10;



    public function __construct(ProcedureMap $procedureMap, ContainerInterface $container, GUIRequest $request, Controller $server)
    {
        $this->procedureMap = $procedureMap;
        $this->container = $container;
        $this->request = $request;
        $this->server = $server;
    }

    public function run()
    {
        $this->product = $this->procedureMap->getProducts()[0];
        $this->gui = WindowFactory::create();
//        Debug::set($this->gui, $this->container);
        $this->setUpGuiEnvironment();
        $this->gui->on('start', function () {
            $this->firstRequest();
        });
        $this->gui->run();
    }


    public function doRequest($cmd)
    {
        $this->request->prepareReq($cmd);
        $this->request->setProduct($this->product);
        try {
            $this->server->run();
            $this->request->reset();
        } catch (\Exception $e) {
            $this->alert($e->getMessage());
        }
    }

    public function alert(string $msg)
    {
        $this->gui->alert($msg);
    }

    public function destroyObject(VisualObjectInterface $object)
    {
        $this->gui->destroyObject($object);
    }

    private function firstRequest()
    {
        $this->doRequest(AppMsg::GUI_INFO);
    }

    public function update()
    {
        $this->doRequest(AppMsg::FORWARD);
    }

    public function addProduct()
    {
        $this->doRequest(AppMsg::CREATE_NEW_ONE_PRODUCT);
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getLoop(): LoopInterface
    {
        return $this->gui->getLoop();
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getProductsPerPage(): int
    {
        return $this->productsPerPage;
    }


    private function setUpGuiEnvironment()
    {
        $this->container->set(LoopInterface::class, $this->gui->getLoop());
        $this->setSubscribersToEventChannel();
    }

    private function setSubscribersToEventChannel()
    {
        $this->container->get(EventChannel::class)->subscribeArray([
            $this->container->get(ModeManager::class),
            $this->container->get(ProductTableComposer::class),
            $this->container->get(ProductTableSynchronizer::class),
        ]);
    }

}