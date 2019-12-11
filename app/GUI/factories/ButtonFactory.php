<?php


namespace App\GUI\factories;


use Gui\Components\Button;

class ButtonFactory
{

    public static function create(array $offsets, array $sizes, array $additions): Button
    {
        return new Button(array_merge($offsets, $sizes, $additions));

    }

//    public static function create(string $text, $left, $top, $height, $width): Button
//    {
//        return new Button([
//            'value' => $text,
//            'width' => $width,
//            'height' => $height,
//            'top' => $top,
//            'left'=> $left,
//        ]);
//
//    }
}