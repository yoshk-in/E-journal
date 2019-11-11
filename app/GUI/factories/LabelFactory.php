<?php


namespace App\GUI\factories;


use App\GUI\components\LabelWrapper;
use App\GUI\Color;

class LabelFactory
{
    static private $color = Color::WHITE;

    public static function create(string $text, string $fontSize, int $width, int $height, $top, $left): LabelWrapper
    {
        $labelWrapper =  new LabelWrapper(
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
        return $labelWrapper->setTop($top);

    }

}