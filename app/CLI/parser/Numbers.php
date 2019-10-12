<?php


namespace App\CLI\parser;


use App\cache\Cache;

class Numbers extends Parser
{
    const EMPTY_PART_NUMBER = 'не задан номер партии - его можно сохранить единожды, чтобы не вводить каждый раз, командой вида "партия \'120\'""';
    const REPEAT = 'переданы повторяющиеся номера';
    const ERR_NUMBERS_DESC = 'диапазон номеров должен задаваться по возврастающей';
    const EMPTY_NUMBERS = 'не заданы номера блоков в запросе';
    const WRONG_NUMBER_LENGTH = ' номера блоков должны задаваться тремя или шестью цифрами';

    private $result = [];
    const COMMA = ',';
    const HYPHEN = '-';

    private $request;
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }


    public function doParse($request)
    {
        $this->request = $request;
        $numbers_string = $this->request->getCLIArgs()[self::$argN] ?? $this->exception(self::EMPTY_NUMBERS);
        $this->parseEachNumberOrRangeNumbers(explode(self::COMMA, $numbers_string));
        count($this->result) == count(array_unique($this->result)) || $this->exception(self::REPEAT);
        sort($this->result, SORT_NUMERIC);
        $request->setBlockNumbers($this->result);
    }

    protected function parseEachNumberOrRangeNumbers(array $explodedByComma)
    {
        foreach ($explodedByComma as $part) {
            $arrayByHyphen = explode(self::HYPHEN, $part);
            if (count($arrayByHyphen) == 2) {
                [$first, $last] = $this->getFullNumbers($arrayByHyphen);
               $first < $last || $this->exception( self::ERR_NUMBERS_DESC);
                $numberRange[] = range($first, $last);
            } else {
                $numbers = $this->getFullNumbers(array_merge($numbers ?? [], $arrayByHyphen));
            }
        }
        $this->result = array_merge($numbers ?? [], ...$numberRange ?? []);
    }


    protected function getFullNumbers(array $numbers): array
    {
        foreach ($numbers as $number) {
            $full_numbers[] = (($length = strlen($number)) === 6) ? $number :
                !($length === 3 || $this->exception(self::WRONG_NUMBER_LENGTH)) ?:
                ($this->cache->getParty($this->request->getProduct())
                    ?? $this->exception(self::EMPTY_PART_NUMBER))
                . $number;
        }
        return $full_numbers ?? [];
    }

}