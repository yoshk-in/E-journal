<?php


namespace App\console\parser;


use App\base\ConsoleRequest;

abstract class CommandParserByNumbersParser extends CommandParser
{
    private $numbersParser;
    private $numbers;
    private $nextArg = NextArgIndexMap::NUMBERS;

    public function __construct(ConsoleRequest $request, NumbersParser $numbersParser)
    {
        parent::__construct($request);
        $this->numbersParser = $numbersParser;
    }

    public function parse()
    {
        $this->numbers = $this->numbersParser->parse(
            $this->request->getConsoleArgs()[$this->nextArg],
            $this->request->getProductName()
        );
    }

    public function getBlockNumbers(): array
    {
        return $this->numbers;
    }

}