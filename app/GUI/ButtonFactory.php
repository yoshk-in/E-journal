<?php


namespace App\GUI;


use Gui\Components\Button;

class ButtonFactory
{
    public static function createWithOn(\Closure $clickCallback): Button
    {
        $button = new Button([
            'value' => 'отправить',
            'top' => 300,
            'left' => 300,
            'width' => 400,
            'height' => 200
        ]);
        $button->on('mousedown', $clickCallback);
        return $button;
    }
}