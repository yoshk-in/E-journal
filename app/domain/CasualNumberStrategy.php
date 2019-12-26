<?php


namespace App\domain;


class CasualNumberStrategy extends NumberStrategy
{

    public function setProductNumber( $product, $number,  $mainNumber)
    {
        if ($mainNumber) throw new \Exception( __CLASS__ . ': number had already set');
        $product->setNumbers($number, $number);
    }

    public function getNumber($product): int
    {
        return $product->getNumbersToStrategy()[0];
    }

    public function nextNumber(Product $product): int
    {
        return $product->getNumbersToStrategy()[0] + 1;
    }

    public function getAdvancedNumber(Product $product)
    {
        return $product->getNumbersToStrategy()[0];
    }
}