<?php


namespace App\domain\numberStrategy;


class DoubleNumberStrategy extends NumberStrategy
{


    public static function initProductNumbers($number): array
    {
        return [null,  $number, $number];
    }


    public static function nextMainNumber(?int $mainNumber, int $advancedNumber): ?int
    {
        return is_null($mainNumber) ? null : $mainNumber + 1;
    }


    static function isDoubleNumber(): bool
    {
        return true;
    }


    static public function changeMainNumber(?int $mainNumber, int $toMain): array
    {
        assert(!is_null($toMain), ' double numbering product main number has to change on not null');
        return [$toMain, $toMain];
    }


    static function getNumberInitLength(int $mainNumberLength, int $partNLength, int $preNumberLength): array
    {
        return [$preNumberLength,  null];
    }

    static function getAnyNumber(int $main, int $pre): int
    {
        return $main ?? $pre;
    }
}