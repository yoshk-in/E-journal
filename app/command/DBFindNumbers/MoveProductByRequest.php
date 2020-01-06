<?php


namespace App\command\DBFindNumbers;


use App\base\AbstractRequest;
use App\base\CLIRequest;
use App\domain\Product;

class MoveProductByRequest extends FindNumbersCmd
{
    /** @var AbstractRequest | CLIRequest */
    protected AbstractRequest $request;

    protected string $moveDirect;
    protected string $product;
    protected \Closure $notFoundHandler;


    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $startProcedure = function ($notFounds, $procedure) {
            $this->checkInput(is_null($procedure), self::ERR_NOT_INIT, $notFounds);
            foreach ($this->productRepository->createProducts($notFounds, $this->product) as $product)
            {
                $product->{$this->moveDirect}($procedure);
            }
        };
        $endProcedure = fn ($notFound) => $this->checkInput(false, self::ERR_NOT_INIT, $notFound);
        $this->notFoundHandler = ${$this->moveDirect = $this->request->getMoveProductMethod()};
        parent::doExecute($productName, $numbers, $procedure);
    }

    protected function doWithFound(Product $product, ?string $procedure = null)
    {
        $product->{$this->moveDirect}($procedure);
    }


    protected function doWithNotFounds(array $not_founds, $procedure)
    {
        $this->{$this->notFoundHandler}($not_founds, $procedure);
    }
}