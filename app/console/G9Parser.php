<?php

namespace App\console;

use App\base\AppHelper;

class G9Parser extends ConsoleSyntaxParser
{
    private $arg1;
    private $arg2;
    private $arg3;
    private $cache;

    public function __construct()
    {
        parent::__construct();
        $i = 0;
        foreach ($_SERVER['argv'] as $arg) {
            $prop = "arg" . $i;
            $this->$prop = $arg;
            ++$i;
        }
        $this->cache = AppHelper::getCacheObject();
    }

    protected  function doParse()
    {
        if ($this->arg2) {
            $this->setCommand();
        }

        if (!is_null($this->arg3)) {
            $numbers = $this->parseBlocksNumbers($this->arg3);
            $this->request->setBlockNumbers($numbers);
        }

    }

    protected function setCommand()
    {
        $arg2 = $_SERVER['argv'][2];

        if ($arg2 === '+') {
            $this->ensure(!is_null($this->arg3), 'введите номера блоков');
            $this->request->addCommand('addObject');
            $this->request->addCommand('nextWorkStageG9');
            return;
        };
        if (mb_stripos($arg2, 'партия=') !== false) {
            list($key, $value) = explode('=', $arg2);
            $this->ensure(strlen($value) === 3);
            $this->request->addCommand('setPartNumber');
            $this->request->setPartNumber($value);
            return;
        }

        if (mb_stripos($arg2, 'стат')!== false) {
            if ($this->arg3) {
                $this->request->addCommand('printRangeStat');
            } else {
                $this->request->addCommand('printFullStat');
            }
            return;
        }
        $this->ensure(false);

    }

    protected function parseBlocksNumbers(string $arg)
    {
        $arrayOfNumbers = [];
        $raw = $this->explodeByComma($arg);
        foreach ($raw as $line) {
            if (strpos($line, '-')) {

                $this->ensure(substr_count($line, '-') === 1);
                $range = self::explodeByHyphen($line);

                list($first, $last) = $this->getFullNumbers($range);
                $this->ensure($first < $last);

                for ($i = $first; $i <= $last; $i++) {
                    $arrayOfNumbers[] = (int)$i;
                }
            } else {
                $fullNumbers = $this->getFullNumbers(array($line));
                $arrayOfNumbers = array_merge($arrayOfNumbers, $fullNumbers);
            }
        }
        return $arrayOfNumbers;
    }

    protected function ensure(bool $condition, $msg = 'неверно заданы параметры запроса')
    {
        if (!$condition) {
            throw new \App\base\AppException($msg);
        }
    }

    protected function getFullNumbers($numbers)
    {
        $fullNumbers = [];
        foreach ($numbers as $number) {
            $this->ensure(is_int((int)$number));
            $number = (string)$number;
            if (strlen($number) === 6) {
                $partNumber = substr($number, 0, 3);
                (AppHelper::getCacheObject())->setPartNumber($partNumber);
                $fullNumbers[] = (int) $number;
            } else if (strlen($number) === 3) {
                $partNumber = (AppHelper::getCacheObject())->getPartNumber();
                $fullNumbers[] = (int) (((string) $partNumber) . $number);

            } else {
                $this->ensure(false);
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
