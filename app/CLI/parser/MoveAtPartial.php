<?php


namespace App\CLI\parser;


use App\base\AppCmd;
use Exception;

class MoveAtPartial extends MoveParser
{


    protected function doParse()
    {
        $this->request->addCmd(AppCmd::START_PROCEDURE);
        $this->request->setMoveProductMethod(self::DIRECTIONS['+']);
        if (! $partial = $this->getCurrentCLIArg()) throw new Exception(self::EMPTY_CMD);
        $this->request->setPartial($this->parserBuffer->partialAliases[$partial]);
    }
}