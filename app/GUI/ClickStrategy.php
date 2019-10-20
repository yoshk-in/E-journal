<?php


namespace App\GUI;


use Gui\Components\VisualObject;

class ClickStrategy
{
    public function getClickHandler($emitter): \Closure
    {
        return function () use ($emitter) {
            static $i = 0;
            $i++;
            if ($i % 2 === 0) {
                $emitter->setBackgroundColor(Color::GREEN);
            } else $emitter->setBackgroundColor(Color::YELLOW);
        };
    }
}