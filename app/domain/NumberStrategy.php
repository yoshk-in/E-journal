<?php


namespace App\domain;


abstract class NumberStrategy
{
    abstract static public function setProductNumber(Product $product, int $number, ?int $mainNumber);

    abstract static public function getNumber(Product $product);

    abstract static public function nextNumber(Product $product);

    abstract static public function getAdvancedNumber(Product $product);

    abstract static function isDoubleNumber(): bool;
}