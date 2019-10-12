<?php


namespace App\CLI\parser;


use App\base\CLIRequest;
use App\base\exceptions\WrongInputException;
use App\controller\TChainOfResponsibility;

abstract class Parser
{
    use TChainOfResponsibility;

    static $argN = 1;
    const MAIN_ERROR = 'неверно заданы параметры запроса: ';



    public function parse(CLIRequest $request)
    {
        $this->doParse($request);
        ++self::$argN;
        $this->next->parse($request);
    }

    protected function exception(string $msg): \Exception
    {
        throw new WrongInputException(self::MAIN_ERROR . $msg);
    }

    abstract protected function doParse(CLIRequest $request);

}