<?php

namespace App\base;

class AppHelper
{
    private static $request;
    private static $journalPath = 'mmz/journal/';

    public static function getRequest(): Request
    {
        if (is_null(self::$request)) {
            self::$request = new Request();
        }
        return self::$request;
    }

    public function getConsoleSyntaxParser()
    {
        if (($_SERVER['argv'][1] === 'г9') or ($_SERVER['argv'][1] === 'Г9')) {
            return new \App\console\G9Parser();
        }

    }

    public static function getRootDir()
    {
        $dir       = __DIR__;
        $parentDir = strstr($dir, self::$journalPath, true);
        return $parentDir . self::$journalPath;
    }
}
