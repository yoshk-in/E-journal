<?php


namespace App\GUI\factories;


use Gui\Application;

class GuiFactory
{
    public static function create(int $left, int $top, int $width, int $height): Application
    {
        $app =  new Application([
            'title' => 'ЖУРНАЛ УЧЕТА',
            'left' => $left,
            'top' => $top,
            'width' => $width,
            'height' => $height,
        ]);
        $app->setVerboseLevel(0);
        return $app;
    }
}