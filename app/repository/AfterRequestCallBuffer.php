<?php


namespace App\repository;


class AfterRequestCallBuffer
{
    protected static array $buffer = [];

    public static function set(callable $closure)
    {
        self::$buffer[] = $closure;
    }




    public static function drop()
    {
        foreach (self::$buffer as $call) {
            $call();
        }
    }
}