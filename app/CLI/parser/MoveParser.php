<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class MoveParser extends Parser
{
    const DIRECTIONS = [
        '+' => 'startProcedure',
        '-' => 'endProcedure'
    ];

    const EMPTY_CMD = 'укажите название процедуры';

    protected function doParse()
    {
        $this->request->addCmd(AppMsg::MOVE_PRODUCT);
        $this->request->setMoveProductMethod(
            self::DIRECTIONS[$this->parserBuffer->cmdArg ?? $this->exception(self::EMPTY_CMD)]
        );
    }
}