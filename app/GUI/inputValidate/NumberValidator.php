<?php


namespace App\GUI\inputValidate;


use App\domain\procedures\ProductMap;

class NumberValidator
{

    /**
     * @var ProductMap
     */
    private $productMap;

    public function __construct(ProductMap $productMap)
    {
        $this->productMap = $productMap;
    }

    public function isValidMainNumber(string $product, $input): bool
    {
        return strlen((string)$input) === $this->productMap->getMainNumberLength($product)
        && is_int((int)$input) ?
            true : false;
    }


}