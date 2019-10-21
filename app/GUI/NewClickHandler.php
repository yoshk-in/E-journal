<?php


namespace App\GUI;


use Gui\Components\VisualObject;

class NewClickHandler
{
    public static function handle(VisualObject $emitter)
    {
        static $i = 0;
        $i++;
        if ($i % 2 === 0) {
            $emitter->setBorderColor(Color::BLACK);
        } else $emitter->setBorderColor(Color::YELLOW);

    }

}