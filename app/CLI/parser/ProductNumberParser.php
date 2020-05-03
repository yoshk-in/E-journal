<?php


namespace App\CLI\parser;


class ProductNumberParser extends Parser
{
    const EMPTY_NUMBERS = 'не заданы номера блоков в запросе';

    const COMMA = ',';
    const HYPHEN = '-';



    public function doParse()
    {
        $numbers_string = $this->getCurrentCLIArg() ?? $this->exception(self::EMPTY_NUMBERS);
        $this->parseEachNumberOrRangeNumbers(explode(self::COMMA, $numbers_string));
    }

    protected function parseEachNumberOrRangeNumbers(array $explodedByComma)
    {
        foreach ($explodedByComma as $part) {
            $arrayByHyphen = explode(self::HYPHEN, $part);
            if (count($arrayByHyphen) == 2) {
                $this->request->addProductDataRange(...$arrayByHyphen);
            } else {
                $this->request->addProductData(...$arrayByHyphen);
            }
        }
    }



}