<?php


namespace App\GUI;


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

    static function table()
    {
        return new Table(20, 20, 100, 200, 600, self::$container->get(MouseMng::class));
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