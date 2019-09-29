<?php


namespace App\console\parser;


use App\base\exceptions\WrongInputException;


class SetPartNumberParser extends CommandParser
{
    private $partNumber;

    public function parse()
    {
        $arg = $this->request->getConsoleArgs()[NextArgIndex::PART_NUMBER];
        $this->validatePartNumber($arg);
        $this->partNumber = $arg;
    }

    public function getPartNumber(): string
    {
        return $this->partNumber;
    }

    private function validatePartNumber(string $arg) : ?WrongInputException
    {
        if (strlen($arg) == 3) return null;
        throw new WrongInputException(' номер партии должен сосоять из трех цифр');
    }
}