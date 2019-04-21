<?php


namespace App\console;


class ParserResolver
{
    public static function getConsoleSyntaxParser()
    {
        if (isset($_SERVER['argv'][1])) {
            if (mb_stripos($_SERVER['argv'][1], 'г9') === 0) {
                return new \App\console\G9Parser();
            }
        }
        throw new \App\base\AppException('неизвестная команда');
    }
}