<?php


namespace App\GUI\factories;


use Gui\Components\Button;

class ButtonFactory
{
    public static function createWithOn(\Closure $clickCallback, $offset, $top, string $text): Button
    {
        $button = new Button([
            'value' => $text,
            'top' => $top,
            'left'=> $offset,
            'width' => 300,
            'height' => 70
        ]);
        $button->on('mousedown', $clickCallback);
        return $button;
    }
}