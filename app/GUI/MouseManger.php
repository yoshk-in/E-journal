<?php


namespace App\GUI;


class MouseManger
{
    private static $currentStrategy = ClickHandler::class;


    public static function changeHandler(string $strategy): void
    {
       self::$currentStrategy = $strategy;
    }

    public static function getHandler()
    {
        return self::$currentStrategy;
    }
}