<?php


namespace App\CLI\parser;


use App\base\CLIRequest;
use App\base\exceptions\WrongInputException;
use App\CLI\parser\buffer\ParserBuffer;
use App\domain\ProductMap;

class PartNumberValidator extends Parser
{
    const ERR_COUNT = ' номер партии должен состоять из трех цифр';
    const EMPTY_NUMBER = 'укажите номер партии';
    private ProductMap $productMap;


    public function __construct(CLIRequest $request, ParserBuffer $parserBuffer, ProductMap $productMap)
    {
        parent::__construct($request, $parserBuffer);
        $this->productMap = $productMap;
    }

    protected function doParse()
    {
        $arg = $this->getCurrentCLIArg() ?? $this->exception(self::EMPTY_NUMBER);
        $this->validatePartNumber($arg);
        $this->request->setParty($arg);
    }



    private function validatePartNumber(string $arg) : ?WrongInputException
    {
        if (strlen($arg) == $this->productMap->getPartNumberLength()) return null;
        $this->exception(self::ERR_COUNT);
    }
}