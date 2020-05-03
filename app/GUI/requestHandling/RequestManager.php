<?php


namespace App\GUI\requestHandling;


use App\base\AppCmd;
use App\base\exceptions\WrongInputException;
use App\base\GUIRequest;
use App\controller\Controller;
use App\domain\AbstractProduct;
use App\domain\ProductMap;
use App\events\Event;
use App\events\IEvent;
use App\events\EventChannel;
use App\events\IObservable;
use App\events\TObservable;
use App\GUI\Debug;
use App\GUI\handlers\Alert;
use App\GUI\inputValidate\NumberValidator;
use App\GUI\UserActionMng;
use Closure;

class RequestManager implements IObservable, IEvent
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

    public function addData(AbstractProduct $product)
    {
        $this->addProductStrategy->addProductToRequestBuffer($product);
    }

    public function addChangedMainNumber($number, AbstractProduct $product)
    {
        $this->addDoubleNumberProduct->addChangedMainNumber($this, $number, $product);
    }

    public function addChangeMainNumberCmd(int $advancedNumber, int $mainNumber)
    {
        $this->request->addCmd(AppCmd::CHANGE_PRODUCT_MAIN_NUMBER);
        $this->request->addChangingNumber($advancedNumber, $mainNumber);
    }


    public function unsetData(AbstractProduct $product)
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
        $this->doRequestByBufferNumbers(AppCmd::FIND_UNFINISHED);
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
            $this->doRequestByBufferNumbers(AppCmd::FORWARD);
        } else {
            $this->request->prepareProductRequest($this->getProduct());
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
        $this->request->setProductNumbers($number);
        $this->request($cmd);
    }

    protected function request($cmd, Closure $finally = null)
    {
        $this->request->addCmd($cmd);
        $this->request->prepareProductRequest($this->currentProduct);
        $this->catchWrongInput(fn() => $this->backend->run(), $finally);
    }

    protected function resetRequestAndSelectedCells()
    {
        $this->request->reset();
        $this->mouseMng->getHandler()->resetSelectedCells();
    }


    public function alert(string $msg)
    {
        $this->channel->update(new Event($msg, $this));
    }

    private function catchWrongInput(Closure $action, Closure $finally = null)
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