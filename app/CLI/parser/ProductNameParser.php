<?php


namespace App\CLI\parser;


use App\base\CLIRequest;
use App\base\exceptions\WrongInputException;
use App\domain\procedures\ProcedureMap;
use App\CLI\parser\buffer\ParserBuffer;

class ProductNameParser extends Parser
{

    private ProcedureMap $procedureMap;

    public function __construct(ProcedureMap $procedureMap, CLIRequest $request, ParserBuffer $parserBuffer)
    {
        $this->procedureMap = $procedureMap;
        parent::__construct($request, $parserBuffer);
    }

    protected function doParse()
    {
        $product_name = $this->request->getCLIArgs()[self::$argN];
        $this->request->prepareProductRequest($product_name);
        $this->setPartialsToCommandMap();
    }

    protected function setPartialsToCommandMap()
    {
        foreach ($this->procedureMap->getAllPartialNamesWithAliases() as [$short_name, $partial]) {
            $adds[$short_name] = CommandParseMap::PARTIAL_MOVE_BLOCK;
            $adds[$partial] = CommandParseMap::PARTIAL_MOVE_BLOCK;
            $partialAliases[$short_name] = $partial;
            $partialAliases[$partial] = $partial;
        }
        $this->parserBuffer->additionToMap = $adds ?? [];
        $this->parserBuffer->partialAliases = $partialAliases ?? [];

    }


}