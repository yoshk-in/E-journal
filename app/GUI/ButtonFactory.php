<?php


namespace App\GUI;


use Gui\Components\Button;

class ButtonFactory
{
    public static function createWithOn(\Closure $clickCallback, $offset = 600): Button
    {
        $button = new Button([
            'value' => 'отправить',
            'top' => 580,
            'left'=> $offset,
            'width' => 300,
            'height' => 70
        ]);
        $button->on('mousedown', $clickCallback);
        return $button;
    }
}