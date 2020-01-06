<?php


namespace App\CLI\parser;


use App\base\CLIRequest;
use App\base\exceptions\WrongInputException;
use App\domain\ProcedureMap;
use App\CLI\parser\buffer\ParserBuffer;

class ProductNameParser extends Parser
{
    const WRONG_NAME = 'наименование блока или не задано, или задано неверно';

    private ProcedureMap $procedureMap;

    public function __construct(ProcedureMap $procedureMap, CLIRequest $request, ParserBuffer $parserBuffer)
    {
        $this->procedureMap = $procedureMap;
        parent::__construct($request, $parserBuffer);
    }

    protected function doParse()
    {
        $product_name = mb_strtoupper($this->request->getCLIArgs()[self::$argN]);
        if (!in_array($name ?? '', $this->procedureMap->getProducts())) {
            throw new WrongInputException(self::WRONG_NAME);
        }
        $this->request->setProduct($product_name);
        $this->setPartialsToCommandMap();
    }

    protected function setPartialsToCommandMap()
    {
        foreach ($this->procedureMap->getAllDoublePartialNames($this->request->getProduct()) as [$short_name, $partial]) {
            $adds[$short_name] = CommandParseMap::PARTIAL_MOVE_BLOCK;
            $adds[$partial] = CommandParseMap::PARTIAL_MOVE_BLOCK;
            $partialAliases[$short_name] = $partial;
            $partialAliases[$partial] = $partial;
        }
        $this->parserBuffer->additionToMap = $adds ?? [];
        $this->parserBuffer->partialAliases = $partialAliases ?? [];

    }


}