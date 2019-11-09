<?php


namespace App\GUI;


use Gui\Application;
use Gui\Components\Label;

class Debug
{
    private static $gui;

    static function set(Application $gui)
    {
        self::$gui = $gui;
    }

    static function print($text)
    {
        switch (gettype($text)) {
            case 'string':
                break;
            default:
                $text = json_encode((array)$text, true);
        }

       self::$gui->alert($text);
    }

}