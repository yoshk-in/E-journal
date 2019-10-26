<?php


namespace App\GUI;


use Gui\Components\VisualObject;

abstract class ClickHandler
{

    protected static $nextColor = [
        State::COLOR[0] => State::COLOR[1],
        State::COLOR[1] => State::COLOR[2]
    ];


    protected $counter;

    abstract public static function handle(Shape $emitter, string $prevColor);

}