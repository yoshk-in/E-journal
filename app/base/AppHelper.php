<?php

namespace App\base;



class AppHelper
{
    private static $request;


    public static function getRequest(): Request
    {
        if (is_null(self::$request)) {
            self::$request = new Request();
        }
        return self::$request;
    }

    public function getConsoleSyntaxParser()
    {
        if (isset($_SERVER['argv'][1])) {
            if (mb_stripos($_SERVER['argv'][1], 'г9') === 0) {
                return new \App\console\G9Parser();
            }
        }

    }

    public static function getCacheObject()
    {
        return \App\cache\Cache::init();
    }

    public static function getCommandResolver()
    {
        return \App\command\CommandResolver::class;
    }
}
