<?php

namespace App\console;

use App\base\AppHelper;
use App\base\exceptions\AppException;

class G9Parser extends ConsoleParser
{
    private $_arg1;

    private $_arg2;

    private $_arg3;

    private $_cache;

    public function __construct()
    {
        parent::__construct();
        $args_counter = 0;
        foreach ($_SERVER['argv'] as $argN) {
            $prop_object = "_arg" . $args_counter;
            $this->$prop_object = $argN;
            ++$args_counter;
        }
        $this->_cache = AppHelper::getCacheObject();
    }


    protected function doParse()
    {
        if ($this->_arg1) {
            if ('г9' === mb_strtolower($this->_arg1)) $target = 'App\domain\GNine';
            else $target = $this->_arg1;
            $this->request->setProperty('targetClass', $target);
        } else $this->ensure(false);
        $this->setCommand();
        if ($this->isBlockNumbers($this->_arg3)) {
            $numbers = $this->parseBlocksNumbers($this->_arg3);
            $this->request->setBlockNumbers($numbers);
        }
    }

    protected function setCommand()
    {
        switch ($this->_arg2) {
            case 'очистка' :
                $this->request->addCommand('clearJournal');
                return;
            case 'приход' :
                $this->request->addCommand('blocksAreArrived');
                return;
            case 'вынос' :
                $this->request->addCommand('blocksAreDispatched');
                return;
            case null :
                $this->request->addCommand('printFullStat');
                return;
        }
        if ($tt = $this->isTTProc($this->_arg2)) {
            $this->request->addCommand('blocksAreArrived');
            $this->request->addTTCommand($tt);
            return;
        }
        if (mb_stripos($this->_arg2, 'партия=') !== false) {
            $value = (explode('=', $this->_arg2))[1];
            $this->ensure(strlen($value) === 3);
            $this->request->addCommand('setPartNumber');
            $this->request->setPartNumber($value);
            return;
        }
        if ($this->isBlockNumbers($this->_arg2)) {
            $this->request->addCommand('printRangeStat');
            $this->request->setBlockNumbers($this->parseBlocksNumbers($this->_arg2));
            return;
        }

        $this->ensure(false);
    }

    protected function parseBlocksNumbers(string $arg)
    {
        $numbers_array = [];
        $raw_data = $this->explodeByComma($arg);
        foreach ($raw_data as $line_data) {
            if (strpos($line_data, '-')) {
                $this->ensure(substr_count($line_data, '-') === 1);
                $range = self::explodeByHyphen($line_data);
                list($first, $last) = $this->getFullNumbers($range);
                $this->ensure($first < $last);
                $numbers_array = array_merge($numbers_array, range($first, $last));
            } else {
                $full_numbers = $this->getFullNumbers([$line_data]);
                $numbers_array = array_merge($numbers_array, $full_numbers);
            }
        }

        $this->ensure(
            count($numbers_array) == count(array_unique($numbers_array)),
            'переданы повторяющиеся номера'
        );
        sort($numbers_array, SORT_NUMERIC);
        return $numbers_array;
    }

    protected function ensure(bool $condition, $msg = 'неверно заданы параметры запроса')
    {
        if (!$condition) throw new AppException($msg);
    }

    protected function getFullNumbers($numbers)
    {
        $full_numbers = [];
        foreach ($numbers as $number) {
            $this->ensure(is_int((int)$number));
            $number = (string)$number;
            if (strlen($number) === 6) {
                $part_number = substr($number, 0, 3);
                (AppHelper::getCacheObject())->setPartNumber($part_number);
                $full_numbers[] = (int)$number;
            } else if (strlen($number) === 3) {
                $part_number = (AppHelper::getCacheObject())->getPartNumber();
                $full_numbers[] = (int)(((string)$part_number) . $number);

            } else {
                $this->ensure(false);
            }
        }
        return $full_numbers;
    }

    protected static function explodeByComma(?string $string): array
    {
        return explode(',', $string);
    }

    protected static function explodeByHyphen(?string $string): array
    {
        return explode('-', $string);
    }

    private function isTTProc(string $word)
    {
        $target_class = $this->request->getProperty('targetClass');
        $tt_procs = $target_class::getTTProcedureList('ru');

        if ((!is_null($word)) && in_array($word, $tt_procs))
            return array_flip($tt_procs)[$word];
        return false;

    }

    private function isBlockNumbers(?string $arg)
    {
        $array_by_comma = static::explodeByComma($arg);
        foreach ($array_by_comma as $elem) {
            $array_by_hyphen = static::explodeByHyphen($elem);
            foreach ($array_by_hyphen as $is_number)
                if (!(is_int((int)$is_number) && (strlen($is_number) == 3 || strlen($is_number) == 6))) return false;
        }
        return true;
    }
}

