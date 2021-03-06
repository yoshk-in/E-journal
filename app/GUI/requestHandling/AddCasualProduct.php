<?php


namespace App\GUI\requestHandling;


use App\base\AppCmd;
use App\base\GUIRequest;
use App\domain\procedures\Product;
use App\GUI\inputValidate\NumberValidator;

class AddCasualProduct extends AddProductToRequestStrategy
{
    private string $err = 'номер должен состоять из 6 цифр';



    public function addProductRequest(RequestManager $requestManager, $input)
    {
        if (!empty($input)) {
            $this->validator->isValidMainNumber($requestManager->getProduct(), $input) ?
                $requestManager->requestByNumber(AppCmd::CREATE_PRODUCT_OR_GENERATE, [$input])
                :
                $requestManager->alert($this->err);
        } else {
            $requestManager->doRequestByBufferNumbers(AppCmd::CREATE_PRODUCT_OR_GENERATE);
        }
    }

    public function addProductToRequestBuffer(Product $product)
    {
        $this->request->addBlock($product);
    }

    public function removeProductFromRequestBuffer(Product $product)
    {
        $this->request->removeBlock($product);
    }
}