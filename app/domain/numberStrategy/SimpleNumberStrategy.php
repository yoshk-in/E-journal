<?php


namespace App\domain\numberStrategy;


class SimpleNumberStrategy extends NumberStrategy
{

    public static function initProductNumbers($number): array
    {
        assert(!is_null($number), ' single numbering product has to main number' );
        return [ $number, $number, $number];
    }


    public static function nextMainNumber(?int $mainNumber, int $advancedNumber): int
    {
        assert(!is_null($mainNumber), ' single numbering product has to main number');
        return $mainNumber + 1;
    }


    static function isDoubleNumber(): bool
    {
        return false;
    }


    static public function changeMainNumber(?int $mainNumber, int $toMain): array
    {
        assert(false, 'single numbering product doesnt have to change number');
        return [$mainNumber, $mainNumber];
    }

    static function getNumberInitLength(int $mainNumberLength, int $partNLength, int $preNumberLength): array
    {
        return [$mainNumberLength, $mainNumberLength - $partNLength];
    }

    static function getAnyNumber(int $main, int $pre): int
    {
        return $main;
    }
}