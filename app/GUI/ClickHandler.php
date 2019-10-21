<?php


namespace App\GUI;


use Gui\Components\VisualObject;

class ClickHandler
{
    public static function handle(VisualObject $emitter)
    {
        static $i = 0;
        $i++;
        if ($i % 2 === 0) {
            $emitter->setBackgroundColor(Color::GREEN);
        } else $emitter->setBackgroundColor(Color::YELLOW);

    }
}