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
}
