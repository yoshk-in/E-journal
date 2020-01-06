<?php


namespace App\CLI\parser;


use App\base\exceptions\WrongInputException;

class PartNumberValidator extends Parser
{
    const ERR_COUNT = ' номер партии должен состоять из трех цифр';
    const EMPTY_NUMBER = 'укажите номер партии';

    protected function doParse()
    {
        $arg = $this->getCurrentCLIArg() ?? $this->exception(self::EMPTY_NUMBER);
        $this->validatePartNumber($arg);
        $this->request->setParty($arg);
    }



    private function validatePartNumber(string $arg) : ?WrongInputException
    {
        if (strlen($arg) == 3) return null;
        $this->exception(self::ERR_COUNT);
    }
}