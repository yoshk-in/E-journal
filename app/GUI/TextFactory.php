<?php


namespace App\GUI;


use Gui\Components\Label;


class TextFactory
{
    static private $color = Color::BLACK;

    public static function inMiddle( string $text, string $fontSize, int $width, int $height, $top, $left): Label
    {
        return new Label(
            [
                'text' => $text,
                'fontColor' => self::$color,
                'left' => $left,
                'top' => $top,
//                'width' => $width,   // may be is it not need?
//                'height' => $height,    // may be is it not need?
                'fontSize' => $fontSize,
//                'backgroundColor' => Color::GREEN
            ]

        );

    }

}