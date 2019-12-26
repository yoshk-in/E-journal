<?php


namespace App\domain;


class DoubleNumberStrategy extends NumberStrategy
{

    public function setProductNumber($product, $number, $mainNumber)
    {
        $product->setNumbers(null, $number);
    }

    public function getNumber(Product $product): ?int
    {
        return $product->getNumbersToStrategy()[0];
    }

    public function nextNumber(Product $product): ?int
    {
        if ($product->getNumbersToStrategy()[0] === null) {
            return null;
        } else {
            return $product->getNumbersToStrategy()[0] + 1;
        }
    }

    public function getAdvancedNumber(Product $product): int
    {
        return $product->getNumbersToStrategy()[1];
    }
}