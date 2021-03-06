<?php


namespace App\GUI;

use Closure;
use Gui\Application;
use Psr\Container\ContainerInterface;



class Debug
{
    private static $gui;
    private static $container;

    static function set(Application $gui, ContainerInterface $container)
    {
        self::$gui = $gui;
        self::$container = $container;
    }

    static function getApp()
    {
        return self::$gui;
    }

    static function print($text)
    {
        switch (gettype($text)) {
            case 'string':
                break;
            default:
//                $text = json_encode((array)$text, true);
                $text = (array) $text;
        }

       self::$gui->alert($text);
    }

    static function setTimeout($callback)
    {
        self::$gui->getLoop()->addTimer( 1, $callback);
    }


    static function futureTick(Closure $closure)
    {
        self::$gui->getLoop()->futureTick($closure);
    }
}