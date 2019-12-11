<?php


namespace App\domain;


class CompositeNumberStrategy extends NumberStrategy
{

    public function setProductNumber(Product $product, int $number)
    {
        $product->setNumbers(null, $number);
    }

    public function getNumber(Product $product)
    {
        return $product->getNumbersToStrategy()[0];
    }

    public function nextNumber(Product $product)
    {
        if ($product->getNumbersToStrategy()[0] === null) {
            return null;
        } else {
            return $product->getNumbersToStrategy()[0] + 1;
        }
    }

    public function getAdvancedNumber(Product $product)
    {
        return $product->getNumbersToStrategy()[1];
    }
}