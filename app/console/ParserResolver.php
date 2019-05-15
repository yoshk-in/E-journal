<?php


namespace App\console;

use App\base\exceptions\AppException;


class ParserResolver
{
    public static function getConsoleParser()
    {
        if (isset($_SERVER['argv'][1])) {
            if (mb_stripos($_SERVER['argv'][1], 'г9') === 0) {
                return new \App\console\G9Parser();
            }
        }
        throw new AppException('неизвестная команда');
    }
}