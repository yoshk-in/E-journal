<?php


namespace App\GUI\factories;


use Gui\Components\Select;

class SelectFactory
{
    public static function create(int $left, int $top): Select
    {
        return new Select([
            'left' => $left,
            'top' => $top
        ]);
    }
}