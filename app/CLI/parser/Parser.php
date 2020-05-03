<?php


namespace App\CLI\parser;


use App\base\CLIRequest;
use App\base\exceptions\WrongInputException;
use App\controller\TChainOfResponsibility;
use App\CLI\parser\buffer\ParserBuffer;

abstract class Parser
{
    use TChainOfResponsibility;

    static int $argN = 0;
    const MAIN_ERROR = 'неверно заданы параметры запроса: ';
    protected CLIRequest $request;
    protected ParserBuffer $parserBuffer;


    public function __construct(CLIRequest $request, ParserBuffer $parserBuffer)
    {
        $this->request = $request;
        $this->parserBuffer = $parserBuffer;
    }

    public function parse()
    {
        ++self::$argN;
        $this->doParse();
        $this->next->parse();
    }



    protected function getCurrentCLIArg(): ?string
    {
        return $this->request->getCLIArgs()[self::$argN] ?? null;
    }


    protected function exception(string $msg): WrongInputException
    {
        throw new WrongInputException(self::MAIN_ERROR . $msg);
    }

    abstract protected function doParse();

}