<?php


namespace App\GUI\requestHandling;


use App\base\AppMsg;
use App\base\exceptions\WrongInputException;
use App\base\GUIRequest;
use App\controller\Controller;
use App\domain\CasualNumberStrategy;
use App\domain\Product;
use App\domain\ProductMap;
use App\events\Event;
use App\events\EventChannel;
use App\events\IObservable;
use App\events\TObservable;
use App\GUI\Debug;
use App\GUI\handlers\Alert;
use App\GUI\inputValidate\NumberValidator;
use App\GUI\UserActionMng;

class RequestManager implements IObservable, Event
{
    use TObservable;

    private $request;
    private $backend;
    private $currentProduct;

    private $channel;
    private UserActionMng $mouseMng;
    private AddProductToRequestStrategy $addProductStrategy;
    private AddCasualProduct $addCasualProduct;
    private AddDoubleNumberProduct $addDoubleNumberProduct;
    private ProductMap $productMap;

    public function __construct(ProductMap $productMap,
                                AddCasualProduct $addCasualProduct,
                                AddDoubleNumberProduct $addDoubleNumberProduct,
                                GUIRequest $request,
                                Controller $backend,
                                EventChannel $channel,
                                UserActionMng $mouseMng)
    {
        $this->request = $request;
        $this->backend = $backend;
        $this->channel = $channel;
        $this->mouseMng = $mouseMng;
        $this->addCasualProduct = $addCasualProduct;
        $this->addDoubleNumberProduct = $addDoubleNumberProduct;
        $this->productMap = $productMap;
    }

    public function addData(Product $product)
    {
        $this->addProductStrategy->addProductToRequestBuffer($product);
    }

    public function addChangedMainNumber($number, Product $product)
    {
        $this->addDoubleNumberProduct->addChangedMainNumber($this, $number, $product);
    }

    public function addChangeMainNumberCmd(int $advancedNumber, int $mainNumber)
    {
        $this->request->addCmd(AppMsg::CHANGE_PRODUCT_MAIN_NUMBER);
        $this->request->addChangingNumber($advancedNumber, $mainNumber);
    }


    public function unsetData(Product $product)
    {
        $this->addProductStrategy->removeProductFromRequestBuffer($product);
    }

    public function isCountable(): bool
    {
        return $this->productMap->isCountable($this->getProduct());
    }


    public function getRequest(): GUIRequest
    {
        return $this->request;
    }

    public function newProductRequest()
    {
        $this->doRequestByBufferNumbers(AppMsg::GUI_INFO);
    }

    public function changeProduct(string $product)
    {
        $this->currentProduct = $product;
        $this->addProductStrategy =
            $this->productMap->isDoubleNumbering($product) ? $this->addDoubleNumberProduct : $this->addCasualProduct;
        $this->request->reset();
    }

    public function isDoubleNumberProduct(): bool
    {
        return $this->productMap->isDoubleNumbering($this->getProduct());
    }

    public function moveProductOrPersist()
    {
        if ($this->mouseMng->getHandler()->areSelectedCellsExists()) {
            $this->doRequestByBufferNumbers(AppMsg::FORWARD);
        } else {
            $this->request->setProduct($this->getProduct());
            $this->catchWrongInput(fn() => $this->backend->run(), fn() => $this->resetRequestAndSelectedCells());
        }

    }

    public function getProduct(): string
    {
        return $this->currentProduct;
    }

    public function addProduct($input)
    {
        $this->addProductStrategy->addProductRequest($this, $input);
    }


    public function doRequestByBufferNumbers($cmd)
    {
        $this->request->prepareReqByBufferNumbers();
        $this->request($cmd, fn() => $this->resetRequestAndSelectedCells());
    }

    public function requestByNumber(string $cmd, array $number)
    {
        $this->request->setBlockNumbers($number);
        $this->request($cmd);
    }

    protected function request($cmd, \Closure $finally = null)
    {
        $this->request->addCmd($cmd);
        $this->request->setProduct($this->currentProduct);
        $this->catchWrongInput(fn() => $this->backend->run(), $finally);
    }

    protected function resetRequestAndSelectedCells()
    {
        $this->request->reset();
        $this->mouseMng->getHandler()->removeSelectedCells();
    }


    public function alert(string $msg)
    {
        $this->channel->notify($msg, Event::ALERT);
    }

    private function catchWrongInput(\Closure $action, \Closure $finally = null)
    {
        try {
            $action();
        } catch (WrongInputException $e) {
            $this->alert($e->getMessage());
        } finally {
            is_null($finally) ?: $finally();
        }
    }


}