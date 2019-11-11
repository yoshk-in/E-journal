<?php


namespace App\GUI;


use App\base\AppMsg;
use App\base\GUIRequest;
use App\controller\Controller;
use App\domain\ProcedureMap;
use App\GUI\startMode\FirstStart;
use App\GUI\startMode\NotFirstStart;
use Gui\Application;
use Gui\Components\Shape;
use Psr\Container\ContainerInterface;
use React\EventLoop\LoopInterface;


class GUIManager
{
    private $gui;
    private $request;
    private $response;
    private $server;
    private $procedureMap;
    private $container;
    private $product;
    private $productsPerPage = 10;

    const START_MODE = [
        AppMsg::GUI_INFO => NotFirstStart::class,
        AppMsg::NOT_FOUND => FirstStart::class
    ];


    public function __construct(ProcedureMap $procedureMap, ContainerInterface $container)
    {
        $this->procedureMap = $procedureMap;
        $this->container = $container;
        $this->request = $container->get(GUIRequest::class);
        $this->response = $container->get(Response::class);
    }

    public function run(Controller $app)
    {
        $this->server = $app;
        $this->product = $this->procedureMap->getProducts()[0];
        $this->gui = WindowFactory::create();
        $this->container->set(LoopInterface::class, $this->gui->getLoop());
        $this->container->set(Application::class, $this->gui);
        Debug::set($this->gui, $this->container);
        $this->firstRequest();
        $this->gui->on('start', function () {
            $mode = self::START_MODE[$this->response->getType()];
            $start_mode = $this->container->get($mode);
            $start_mode->run($this->response, $this->gui);
//            $table = Debug::table();
//            $this->shape = $table->addClickTextCell('hi', Color::WHITE);
//
//            $this->shape->on('mousedown', function () {
//                $this->shape->setTop($this->shape->getTop() + 50);
//            });
        });
        $this->gui->run();
    }


    public function doRequest($cmd = AppMsg::FORWARD)
    {
        $this->response->reset();
        $this->request->prepareReq($cmd);
        $this->request->setProduct($this->product);
//        Debug::print($this->request);
        try {
            $this->server->run();
        } catch (\Exception $e) {
            $this->alert($e->getMessage());
        }
        return $this->response;
    }

    public function alert(string $msg)
    {
        $this->gui->alert($msg);
    }

    private function firstRequest()
    {
        $this->doRequest(AppMsg::GUI_INFO);
    }

    public function update()
    {
        $this->doRequest();
        $this->response->reset();
    }


    /**
     * @return GUIRequest|mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getProductsPerPage(): int
    {
        return $this->productsPerPage;
    }


}