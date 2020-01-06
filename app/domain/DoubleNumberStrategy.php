<?php


namespace App\domain;


class DoubleNumberStrategy extends NumberStrategy
{
    private function __construct()
    {
    }

    public static function setProductNumber($product, $number, $mainNumber)
    {
        $product->setNumbers( null, $number);
    }

    public static function getNumber(Product $product): ?int
    {
        return $product->getNumbersToStrategy()[0];
    }

    public static function nextNumber(Product $product): ?int
    {
        if ($product->getNumbersToStrategy()[0] === null) {
            return null;
        } else {
            return $product->getNumbersToStrategy()[0] + 1;
        }
    }

    public static function getAdvancedNumber(Product $product): int
    {
        return $product->getNumbersToStrategy()[1];
    }


    static function isDoubleNumber(): bool
    {
        return true;
    }
}