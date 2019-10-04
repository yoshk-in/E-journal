<?php

namespace App\console\parser;

use App\base\ConsoleRequest;
use App\base\exceptions\WrongInputException;
use App\domain\ProcedureMapManager;

class ConsoleProcMapParser implements ArgMap, NextArgIndexMap
{

    protected $commandMap = [
        '+' =>  ArgMap::BLOCKS_ARE_ARRIVED,
        '-' => ArgMap::BLOCKS_ARE_DISPATCHED,
        'очистка$' => ArgMap::CLEAR_JOURNAL,
        'партия' => ArgMap::SET_PART_NUMBER
        ];

    protected $request;
    protected $procedureMap;
    protected $product;
    private $commandParserResolver;


    public function __construct(ConsoleRequest $request, ProcedureMapManager $procedureMap, CommandParserResolver $commandParserResolver)
    {
        $this->request = $request;
        $this->procedureMap = $procedureMap;
        $this->commandParserResolver = $commandParserResolver;
    }


    public function parseAndFillRequestWithCommands()
    {
        $this->parseProductName();
        $this->request->setProductName($this->product);
        $this->setPartialToCommandMap();
        $parser = $this->parseCmd();
        $this->request->addCommand($parser->getCommand());
        $this->request->setBlockNumbers($parser->getBlockNumbers());
        $this->request->setPartNumber($parser->getPartNumber());
        $this->request->setPartialProcName($parser->getPartial());
    }


    protected function parseCmd(): CommandParser
    {
        $parser = $this->commandParserResolver->getCommandParser($this->request->getConsoleArgs()[2] ?? null, $this->commandMap);
        $parser->parse();
        return $parser;
    }


    protected function setPartialToCommandMap(): void
    {
        $partials = $this->procedureMap->getAllDoublePartialNames($this->product);
//        $casuals = $this->procedureMap->getAllDoubleProcNames($this->product);
        foreach ($partials as [$short_name, $partial]) {
            $this->commandMap[$short_name] = [
                ArgMap::BLOCKS_ARE_ARRIVED
            ];
            $this->commandMap[$partial] = [
                ArgMap::BLOCKS_ARE_ARRIVED
            ];
        }
//        foreach ($casuals as [$short_name, $casual]) {
//            $this->commandMap[$short_name . '+'] = [
//                ArgMap::BLOCKS_ARE_ARRIVED
//            ];
//            $this->commandMap[$casual . '+'] = [
//                ArgMap::BLOCKS_ARE_ARRIVED
//            ];
//            $this->commandMap[$short_name . '-'] = [
//                ArgMap::BLOCKS_ARE_DISPATCHED
//            ];
//            $this->commandMap[$casual . '-'] = [
//                ArgMap::BLOCKS_ARE_DISPATCHED
//            ];
//        }
    }

    protected function parseProductName()
    {
        $product_name = $this->request->getConsoleArgs()[NextArgIndexMap::PRODUCT_NAME] ?? null;
        $this->checkProductName($this->product = mb_strtoupper($product_name), $this->procedureMap->getProductNames());
    }


    protected function checkProductName(?string $name, array $rightNames): ?\Exception
    {
        if (is_null($name)) {
            throw new WrongInputException(' укажите название блока');
        }
        foreach ($rightNames as $right_name) {
            if (preg_match('/' . $right_name . '$/siu', $name)) return null;
        }
        throw new WrongInputException('наименование блока задано неверно');
    }


}
