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

        if ($arg2 === '+') {
            self::ensure(isset($_SERVER['argv'][3]), 'введите параметры запроса');
            self::parseBlocksNumbers($request, $_SERVER['argv'][3]);
            $request->addCommand('addObject');
        };
        if (mb_stripos($arg2, 'партия=') !== false) {
            list($key, $value) = explode('=', $arg2);
            $request->addCommand('setPartNumber');
            $request->setPartNumber($value);
        }

    }

    protected static function parseBlocksNumbers(Request $request, string $arg)
    {
        $arrayOfNumbers = [];
        $raw = self::explodeByComma($arg);
        foreach ($raw as $line) {
            if (strpos($line, '-')) {

                self::ensure(substr_count($line, '-') === 1);
                $range = self::explodeByHyphen($line);

                list($first, $last) = self::getFullNumbers($range);
                self::ensure($first < $last);

                for ($i = $first; $i <= $last; $i++) {
                    $arrayOfNumbers[] = (int)$i;
                }
            } else {
                $fullNumbers = self::getFullNumbers(array($line));
                $arrayOfNumbers = array_merge($arrayOfNumbers, $fullNumbers);
            }
        }
        $request->setBlockNumbers($arrayOfNumbers);
    }

    protected static function ensure(bool $condition, $msg = 'неверно заданы параметры запроса')
    {
        if (!$condition) {
            throw new \App\base\AppException($msg);
        }
    }

    protected static function getFullNumbers($numbers)
    {
        $fullNumbers = [];
        foreach ($numbers as $number) {
            self::ensure(is_int((int)$number));
            $number = (string)$number;
            if (strlen($number) === 6) {
                $partNumber = substr($number, 0, 3);
                (AppHelper::getCacheObject())->setPartNumber($partNumber);
                $fullNumbers[] = (int) $number;
            } else if (strlen($number) === 3) {
                $partNumber = (AppHelper::getCacheObject())->getPartNumber();
                $fullNumbers[] = (int) (((string) $partNumber) . $number);

            } else {
                self::ensure(false);
            }
        }
        return $fullNumbers;
    }

    protected static function explodeByComma(string $string): array
    {
        return explode(',', $string);
    }

    protected static function explodeByHyphen(string $string): array
    {
        return explode('-', $string);
    }
}
