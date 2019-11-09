<?php


namespace App\GUI;


use Gui\Application;

class WindowFactory
{
    public static function create(): Application
    {
        return new Application([
            'title' => 'ЖУРНАЛ УЧЕТА',
            'left' => 248,
            'top' => 50,
            'width' => 1600,
            'height' => 900,
        ]);
    }
}