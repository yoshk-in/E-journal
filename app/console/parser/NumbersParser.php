<?php


namespace App\console\parser;


use App\base\exceptions\AppException;
use App\cache\Cache;

class NumbersParser
{
    const ERROR = false;
    const ERR_MSG = 'не задан номер партии - его можно сохранить единожды, чтобы не вводить каждый раз, командой вида "партия \'120\'""';
    const ERR_REPEAT = 'переданы повторяющиеся номера';
    const MAIN_ERROR = 'неверно заданы параметры запроса: ';
    const ERR_NUMBERS_DESC = 'диапазон номеров должен задаваться по возврастающей';

    private $shortNumbers = [];
    private $numberRange = [];
    private $startString;
    private $arrayByComma = [];
    private $result = [];
    private $product;
    const COMMA = ',';
    const HYPHEN = '-';



    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }


    public function parse(?string $numbers_string, string $product): array
    {
        $this->product = $product;
        $this->startString = $numbers_string;
        $this->stringToArrayByComma();
        $this->parseEachNumberOrRangeNumbers();
        $this->ensure(count($this->result) == count(array_unique($this->result)), self::ERR_REPEAT);
        sort($this->result, SORT_NUMERIC);
        return $this->result;
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

    protected function stringToArrayByComma()
    {
        $this->arrayByComma = explode(self::COMMA, $this->startString);

    }

    protected function explodeByHyphen($part)
    {
        return explode(self::HYPHEN, $part);
    }

    protected function parseEachNumberOrRangeNumbers()
    {
        foreach ($this->arrayByComma as $part) {
            $arrayByHyphen = $this->explodeByHyphen($part);
            if (count($arrayByHyphen) == 2) {
                [$first, $last] = $this->getFullNumbers($arrayByHyphen);
                $this->ensure($first < $last, self::ERR_NUMBERS_DESC);
                $this->numberRange[] = range($first, $last);
            } else {
                $this->shortNumbers = array_merge($this->shortNumbers, $arrayByHyphen);
            }
        }
        $this->result = array_merge($this->getFullNumbers($this->shortNumbers), ...$this->numberRange);
    }


    protected function ensure(bool $condition, ?string $msg = null)
    {
        if (!$condition) throw new AppException(self::MAIN_ERROR . $msg);
    }
}