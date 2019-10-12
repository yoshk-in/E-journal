<?php


namespace App\CLI\parser;


use App\base\exceptions\WrongInputException;
use App\domain\ProcedureMap;

class ProductName extends Parser
{
    const WRONG_NAME = 'наименование блока задано неверно';
    const EMPTY_NAME = ' укажите название блока';

    private $procedureMap;

    public function __construct(ProcedureMap $procedureMap)
    {
        $this->procedureMap = $procedureMap;
    }

    protected function doParse($request)
    {
        $product_name = mb_strtoupper($request->getCLIArgs()[self::$argN] ?? $this->exception(self::EMPTY_NAME));
        $this->checkProduct($product_name, $this->procedureMap->getProducts());
        $request->setProduct($product_name);
    }


    protected function checkProduct(?string $name, array $rightNames): ?\Exception
    {
        if (in_array($name, $rightNames)) return null;
        throw new WrongInputException(self::WRONG_NAME);
    }
}