<?php


namespace App\domain;


abstract class NumberStrategy
{
    abstract public function setProductNumber(Product $product, int $number, ?int $mainNumber);

    abstract public function getNumber(Product $product);

    abstract public function nextNumber(Product $product);

    abstract public function getAdvancedNumber(Product $product);
}