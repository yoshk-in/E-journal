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
            $prop_object = "arg" . $args_counter;
            $this->$prop_object = $argN;
            ++$args_counter;
        }
        $this->_cache = AppHelper::getCacheObject();
    }

    protected function doParse()
    {
        if ($this->_arg2) {
            $this->setCommand();
        }
        if (!is_null($this->_arg3)) {
            $numbers = $this->parseBlocksNumbers($this->_arg3);
            $unique_numbers = array_unique($numbers);
            $this->ensure(
                $unique_numbers == $numbers, 'переданы повторяющиеся номера'
            );
            sort($numbers, SORT_NUMERIC);
            $this->request->setBlockNumbers($numbers);
        }
    }

    protected function setCommand()
    {
        if ($this->_arg2 === '+') {
            $this->ensure(!is_null($this->_arg3), 'введите номера блоков');

            $this->request->addCommand('addObject');
            $this->request->addCommand('nextWorkStageG9');
            return;
        };
        if (mb_stripos($this->_arg2, 'партия=') !== false) {
            $value = (explode('=', $this->_arg2))[1];
            $this->ensure(strlen($value) === 3);
            $this->request->addCommand('setPartNumber');
            $this->request->setPartNumber($value);
            return;
        }

        if (mb_stripos($this->_arg2, 'стат') !== false) {
            if ($this->_arg3) {
                $this->request->addCommand('printRangeStat');
            } else {
                $this->request->addCommand('printFullStat');
            }
            return;
        }

        if ($this->_arg2 === 'очистка') {
            $this->request->addCommand('clearJournal');
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

        return $numbers_array;
    }

    protected function ensure(
        bool $condition, $msg = 'неверно заданы параметры запроса'
    )
    {
        if (!$condition) {
            throw new AppException($msg);
        }
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

    protected static function explodeByComma(string $string): array
    {
        return explode(',', $string);
    }

    protected static function explodeByHyphen(string $string): array
    {
        return explode('-', $string);
    }
}

