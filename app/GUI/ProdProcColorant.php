<?php


namespace App\GUI;


use App\domain\AbstractProcedure;
use App\domain\Product;

class ProdProcColorant
{
    const COLOR = [
        Color::BLACK,
        Color::ORANGE,
        Color::GREEN
        ];

    const NEXT_COLOR = [
        Color::BLACK => Color::ORANGE,
        Color::ORANGE => Color::GREEN,
        Color::GREEN => Color::BLACK
    ];


    public static function color(AbstractProcedure $procedure): string
    {
        return self::COLOR[$procedure->getState()];
    }


    public static function productColor(Product $product): string
    {
        return self::COLOR[$product->getCurrentProc()->getState()];
    }


    public static function nextColor(string $color): string
    {
        return self::NEXT_COLOR[$color];
    }
}