<?php


namespace App\domain;


class CasualNumberStrategy extends NumberStrategy
{

    public static function setProductNumber( $product, $number,  $mainNumber)
    {
        if ($mainNumber) throw new \Exception( __CLASS__ . ': number had already set');
        $product->setNumbers($number, $number);
    }

    public static function getNumber($product): int
    {
        return $product->getNumbersToStrategy()[0];
    }

    public static function nextNumber(Product $product): int
    {
        return $product->getNumbersToStrategy()[0] + 1;
    }

    public static function getAdvancedNumber(Product $product)
    {
        return $product->getNumbersToStrategy()[0];
    }

    static function isDoubleNumber(): bool
    {
        return false;
    }
}