<?php


namespace App\GUI;


use App\domain\procedures\CasualProcedure;
use App\domain\procedures\Product;
use App\GUI\grid\style\Style;

class ProductStateColorant
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


    public function __invoke(CasualProcedure $procedure): string
    {
        return self::color($procedure);
    }

    public static function style(Style $style, CasualProcedure $procedure): Style
    {
        return colorStyle($style, self::color($procedure));
    }



    public static function color(CasualProcedure $procedure): string
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