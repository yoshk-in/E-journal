<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class ArriveAtPartial extends Parser
{
    const EMPTY_PARTIAL = 'укажите название процедуры';
    private $mainParser;


    public function __construct(CLIParser $parser)
    {
        $this->mainParser = $parser;
    }

    protected function doParse($request)
    {
        $request->addCmd(AppMsg::ARRIVE);
        $request->setPartial(
            $test = $this->mainParser->getFullPartial(
            $request->getCLIArgs()[self::$argN] ?? $this->exception(self::EMPTY_PARTIAL)
            )
        );
    }
}