<?php


namespace App\GUI\domainBridge;


use App\base\AppMsg;
use App\base\exceptions\WrongInputException;
use App\base\GUIRequest;
use App\controller\Controller;
use App\domain\ProductMap;
use App\events\Event;
use App\events\EventChannel;
use App\events\IObservable;
use App\events\TObservable;
use App\GUI\Debug;
use App\GUI\handlers\Alert;

class RequestManager implements IObservable, Event
{
    use TObservable;

    private $request;
    private $backend;
    private $productMap;
    private $currentProduct;
    private $err = 'номер должен состоять из 6 цифр';
    private $channel;



    public function __construct(ProductMap $productMap, GUIRequest $request, Controller $backend, EventChannel $channel)
    {
        $this->request = $request;
        $this->backend = $backend;
        $this->productMap = $productMap;
        $this->currentProduct = $this->productMap->first();
        $this->channel = $channel;
    }

    public function getRequest(): GUIRequest
    {
        return $this->request;
    }

    public function firstRequest()
    {
        $this->doRequest(AppMsg::GUI_INFO);
    }

    public function changeProduct(string $product)
    {
        $this->currentProduct = $product;
    }

    public function moveProduct()
    {
        $this->doRequest(AppMsg::FORWARD);
    }

    public function getProduct(): string
    {
        return $this->currentProduct;
    }

    public function addProduct($input)
    {
        $product = $this->getProduct();
        ($input && $this->productMap->getMainNumberLength($product) !== null) ?
            $this->catchWrongInput(\Closure::fromCallable(call_user_func_array([$this, 'casualNumberingProductRequest'], [$product, $input])))
            :
            $this->requestWithNumbers(AppMsg::CREATE_PRODUCTS, [$input]);
    }

    private function casualNumberingProductRequest(string $product, string $input)
    {
        if ($this->productMap->getMainNumberLength($product) !== $input) {
            throw new WrongInputException($this->err);
        }
        $this->requestWithNumbers(AppMsg::CREATE_PRODUCTS, [$input]);
    }

    public function statInfo()
    {
        $this->doRequest(AppMsg::STAT_INFO);
    }

    public function doRequest($cmd)
    {
        $this->request->prepareReq($cmd);
        $this->request->setProduct($this->currentProduct);
        $this->catchWrongInput(\Closure::fromCallable([$this->backend, 'run']));
        $this->request->reset();
    }

    private function requestWithNumbers(string $cmd, array $numbers)
    {
        $this->request->setBlockNumbers($numbers);
        $this->doRequest($cmd);
    }

    private function catchWrongInput(\Closure $action)
    {
        try {
            $action();
        } catch (WrongInputException $e) {
            $this->channel->notify($e->getMessage(), Event::ALERT);
        }
    }


}