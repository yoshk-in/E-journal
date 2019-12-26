<?php


namespace App\GUI\requestHandling;


use App\base\AppMsg;
use App\domain\Product;

class AddDoubleNumberProduct extends AddProductToRequestStrategy
{
    private string $error = ' блоку должен быть назначен номер';

    public function addProductRequest(RequestManager $requestManager, $input)
    {
        if (empty($input)) {
            $requestManager->alert($this->error);
            return;
        }
        $requestManager->requestByNumber(AppMsg::CREATE_PRODUCT_OR_GENERATE, [$input]);
    }

    public function addChangedMainNumber(RequestManager $requestManager, $input, Product $product)
    {
        if (!$this->validator->isValidMainNumber($requestManager->getProduct(), $input)) return;
        $requestManager->addChangeMainNumberCmd($product->getAdvancedNumber(), (int) $input);
    }

    public function addProductToRequestBuffer(Product $product)
    {
        $this->request->addDoubleNumberBlock($product);
    }

    public function removeProductFromRequestBuffer(Product $product)
    {
        $this->request->removeDoubleNumberBlocks($product);
    }
}