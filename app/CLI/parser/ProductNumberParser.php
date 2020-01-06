<?php


namespace App\CLI\parser;


use App\base\CLIRequest;
use App\cache\Cache;

class ProductNumberParser extends Parser
{
    const EMPTY_PART_NUMBER = 'не задан номер партии - его можно сохранить единожды, чтобы не вводить каждый раз, командой вида "партия \'120\'""';
    const REPEAT = 'переданы повторяющиеся номера';
    const ERR_NUMBERS_DESC = 'диапазон номеров должен задаваться по возврастающей';
    const EMPTY_NUMBERS = 'не заданы номера блоков в запросе';
    const WRONG_NUMBER_LENGTH = ' номера блоков должны задаваться тремя или шестью цифрами';

    private array $result = [];
    const COMMA = ',';
    const HYPHEN = '-';

    private Cache $cache;

    public function __construct(Cache $cache, CLIRequest $request)
    {
        $this->cache = $cache;
        parent::__construct($request);
    }


    public function doParse()
    {
        $numbers_string = $this->getCurrentCLIArg() ?? $this->exception(self::EMPTY_NUMBERS);
        $this->parseEachNumberOrRangeNumbers(explode(self::COMMA, $numbers_string));
        count($this->result) == count(array_unique($this->result)) || $this->exception(self::REPEAT);
        sort($this->result, SORT_NUMERIC);
        $this->request->setBlockNumbers($this->result);
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
            $length = strlen($number);
            if ($length === 6) {
                $full_numbers[] = $number;
            } elseif ($length === 3) {
                $partNumber = $this->cachePartNumber() ?? $this->exception(self::EMPTY_PART_NUMBER);
                $full_numbers[] = $partNumber . $number;
            } else $this->exception(self::WRONG_NUMBER_LENGTH);
        }
        return $full_numbers ?? [];
    }

    protected function cachePartNumber(): int
    {
        return $this->cache->getParty($this->request->getProduct());
    }

}