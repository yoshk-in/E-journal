<?php


namespace App\CLI\parser;


use App\base\AppCmd;

class MoveParser extends Parser
{
    const DIRECTIONS = [
        '+' => AppCmd::START_PROCEDURE,
        '-' => AppCmd::END_PROCEDURE
    ];

    const CREATE_NOT_FOUNDS = [
      '+' => true,
      '-' => false
    ];

    const EMPTY_CMD = 'укажите тип("+" или "-") или название процедуры';

    protected function doParse()
    {
        $direction = $this->parserBuffer->cmdArg ?? $this->exception(self::EMPTY_CMD);
        $this->request->addCmd(self::DIRECTIONS[$direction]);
        $this->request->createNotFoundProducts(self::CREATE_NOT_FOUNDS[$direction]);
    }
}