<?php

namespace App\console;

use App\base\Request;
use App\base\AppHelper;

class G9Parser extends ConsoleSyntaxParser
{
    protected static function doParse(Request $request)
    {
        if (isset($_SERVER['argv'][2])) {
            self::setCommand($request);
        }

    }

    protected static function setCommand(Request $request)
    {
        $arg2 = $_SERVER['argv'][2];
        $arg3 = $_SERVER['argv'][3];
        if ($arg2 === '+') {
            self::parseBlocksNumber($request, $arg3);
            $request->setCommand('addObject');
        };
        if (mb_stripos($arg2, 'партия=') !== false) {
            list($key, $value) = explode('=', $arg2);
            $request->setCommand('setPartNumber');
            $request->setPartNumber($value);
        }

    }

    protected static function parseBlocksNumber(Request $request, string $arg)
    {
        $raw = explode(',', $arg);
        $arrayOfNumbers = [];
        $fullNumbers = [];
        var_dump($raw);
        foreach ($raw as $line) {
            if (strpos($line, '-')) {
                var_dump(substr_count($line, '-'));
                self::ensure(substr_count($line, '-') === 1, 'неверно заданы параметры запроса');
                list($first, $last) = explode('-', $line);
                if ($first > $last) {
                    $proxy = $first;
                    $first = $last;
                    $last = $proxy;
                }
                throw new \App\base\AppException('todo here');
                for ($i = $first; $i <= $last; $i++) {
                    $arrayOfNumbers[] = (int)$i;
                }
            } else { $arrayOfNumbers = $raw; }
        }
        var_dump($arrayOfNumbers);
        foreach ($arrayOfNumbers as $block) {
            if (! is_int((int) $block)) self::ensure( false, 'заданы неправильные параметры запроса');
            $block = (string) $block;

            if (strlen($block) === 6) {
                $partNumber = substr($block, 0, 3);
                (AppHelper::getCacheObject())->setPartNumber($partNumber);
                $fullNumbers[] = $block;
            } else if (strlen($block) === 3) {
                $cache = AppHelper::getCacheObject();
                $partNumber = $cache->getPartNumber();
                $fullNumbers[] = ((string) $partNumber) . $block;
            } else { self::ensure(false, 'заданы неправильные параметры запроса'); }
        }
        $request->setBlockNumbers($fullNumbers);
    }

    protected static function ensure(bool $condition, $msg)
    {
        if (!$condition) {
            throw new \App\base\AppException($msg);
        }
    }
}
