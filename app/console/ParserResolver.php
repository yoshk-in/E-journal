<?php


namespace App\console;

use App\base\exceptions\AppException;


class ParserResolver
{
    static $g9parser = 'App\console\G9Parser';

    public static function getConsoleParser($target = null)
    {
        if ($target) return self::$g9parser;

        if (isset($_SERVER['argv'][1])) {
            if (mb_stripos($_SERVER['argv'][1], 'г9') === 0) {
                return new self::$g9parser;
            }
        }
        throw new AppException('неизвестная команда');
    }
}