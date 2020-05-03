<?php


namespace App\domain\numberStrategy;
use App\domain\AbstractProduct;

abstract class NumberStrategy
{
    /**
     * @param int $number
     * @return array[mainNumber, preNumber, preId]
     */
    abstract static public function initProductNumbers(int $number): array;

    abstract static public function nextMainNumber(?int $mainNumber, int $advancedNumber): ?int;

    abstract static public function changeMainNumber(?int $mainNumber, int $toMain): array ;

    abstract static function isDoubleNumber(): bool;

    /**
     * @param int $mainNumberLength
     * @param int $partNLength
     * @param int $preNumberLength
     * @return array[fullLength, shortLength]
     */
    abstract static function getNumberInitLength(int $mainNumberLength, int $partNLength, int $preNumberLength): array;

    abstract static function getAnyNumber(int $main, int $pre): int;
}