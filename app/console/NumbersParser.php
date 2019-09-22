<?php


namespace App\console;


use App\base\exceptions\AppException;
use App\cache\Cache;

class NumbersParser
{
    const ERROR = false;
    const ERR_MSG = 'не задан номер партии - его можно сохранить единожды, чтобы не вводить каждый раз, командой вида "партия \'120\'""';

    private $shortNumbers = [];
    private $numberRange = [];
    private $product;



    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }


    public function parse(?string $numbers_string, string $product)
    {
        $this->product = $product;
        $array_by_comma = $this->stringToArrayByComma($numbers_string);
        $numbers = $this->parseEachNumberOrRangeNumbers($array_by_comma);
        $this->ensure(count($numbers) == count(array_unique($numbers)), 'переданы повторяющиеся номера');
        sort($numbers, SORT_NUMERIC);
        return $numbers;
    }

    protected function getFullNumbers(array $numbers): array
    {
        foreach ($numbers as $number) {
            $full_numbers[] = (strlen($number) === 6) ? $number
                : ($this->cache->getPartNumber($this->product) ?? $this->ensure(self::ERROR, self::ERR_MSG))
                . $number;
        }

        return $full_numbers ?? [];
    }

    protected function stringToArrayByComma(string $numbersString)
    {
        return explode(',', $numbersString);

    }

    protected function explodeByHyphen($part)
    {
        return explode('-', $part);
    }

    protected function parseEachNumberOrRangeNumbers(array $arrayByComma) : array
    {
        foreach ($arrayByComma as $part) {
            $arrayByHyphen = $this->explodeByHyphen($part);
            if (count($arrayByHyphen) == 2) {
                [$first, $last] = $this->getFullNumbers($arrayByHyphen);
                $this->ensure($first < $last, 'диапазон номеров должен задаваться по возврастающей');
                $this->numberRange[] = range($first, $last);
            } else {
                $this->shortNumbers = array_merge($this->shortNumbers, $arrayByHyphen);
            }
        }
        return array_merge($this->getFullNumbers($this->shortNumbers), ...$this->numberRange);
    }


    protected function ensure(bool $condition, ?string $msg = null)
    {
        if (!$condition) throw new AppException('неверно заданы параметры запроса: ' . $msg);
    }
}