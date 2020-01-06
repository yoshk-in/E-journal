<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class MoveAtPartial extends MoveParser
{


    protected function doParse()
    {
        $this->request->addCmd(AppMsg::MOVE_PRODUCT);
        $this->request->setMoveProductMethod(self::DIRECTIONS['+']);
        if (! $partial = $this->getCurrentCLIArg()) throw new \Exception(self::EMPTY_CMD);
        $this->request->setPartial($this->parserBuffer->partialAliases[$partial]);
    }
}