<?php


namespace App\GUI\factories;


use Gui\Components\Button;

class ButtonFactory
{
    public static function createWithOn(string $text, $left, $top, $height, $width, \Closure $clickCallback): Button
    {
        $button = self::create($text, $left, $top, $height, $width);
        $button->on('mousedown', $clickCallback);
        return $button;
    }

    public static function create(string $text, $left, $top, $height, $width): Button
    {
        return new Button([
            'value' => $text,
            'width' => $width,
            'height' => $height,
            'top' => $top,
            'left'=> $left,
        ]);

    }
}