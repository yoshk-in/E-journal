<?php


namespace App\GUI\requestHandling;


use App\base\GUIRequest;
use App\domain\Product;
use App\GUI\inputValidate\NumberValidator;

abstract class AddProductToRequestStrategy
{
    protected GUIRequest $request;
    protected NumberValidator $validator;

    public function __construct(GUIRequest $request, NumberValidator $numberValidator)
    {
        $this->request = $request;
        $this->validator = $numberValidator;
    }

    abstract public function addProductRequest(RequestManager $requestManager, $input);

    abstract public function addProductToRequestBuffer(Product $product);

    abstract public function removeProductFromRequestBuffer(Product $product);
}