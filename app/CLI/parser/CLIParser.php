<?php

namespace App\CLI\parser;

use App\base\CLIRequest;
use App\domain\ProcedureMap;

class CLIParser implements ParseMap
{

    protected $commandMap = [
        '+' =>  ParseMap::BLOCKS_ARE_ARRIVED,
        '-' => ParseMap::BLOCKS_ARE_DISPATCHED,
        'очистка' => ParseMap::CLEAR_JOURNAL,
        'партия' => ParseMap::PARTY
        ];

    protected $procedureMap;
    protected $product;
    private $parseResolver;
    private $partialCache = [];
    const REQUEST_ARG_RESOLVER = 2;


    public function __construct(ProcedureMap $procedureMap, ParseResolver $parseResolver)
    {
        $this->procedureMap = $procedureMap;
        $this->parseResolver = $parseResolver;
    }


    public function parse(CLIRequest $request)
    {
        $this->parseResolver->getProductNameParser()->parse($request);
        $this->setPartialToCommandMap($request);
        $concrete_parser = $this->parseResolver->getParserChain(
            $request->getCLIArgs()[self::REQUEST_ARG_RESOLVER] ?? null,
            $this->commandMap
        );
        $concrete_parser->parse($request);
    }

    public function getFullPartial(string $short)
    {
        return $this->partialCache[$short] ?? $this->exception();
    }


    protected function setPartialToCommandMap(CLIRequest $request): void
    {
        $partials = $this->procedureMap->getAllDoublePartialNames($request->getProduct());
        foreach ($partials as [$short_name, $partial]) {
            $this->commandMap[$short_name] = ParseMap::BLOCKS_ARE_ARRIVED_AT_PARTIAL;
            $this->commandMap[$partial] =  ParseMap::BLOCKS_ARE_ARRIVED_AT_PARTIAL;
            $this->partialCache[$short_name] = $partial;
            $this->partialCache[$partial] = $partial;
        }

    }

    private function exception(): \Exception
    {
        throw new \Exception('partial name not found in ' . __CLASS__ . ' partial cache');
    }




}
