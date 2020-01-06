<?php

namespace App\command;

use App\base\AbstractRequest;
use App\base\exceptions\WrongInputException;


abstract class Command
{

    abstract public function execute();

    protected function checkInput(bool $condition, string $msg = '', ?array $numbers = null)
    {
        $numb_str = $numbers ? implode("\n", $numbers). "\n": '';
        if (!$condition) throw new WrongInputException("неверно заданы параметры запроса: $msg\n $numb_str");
    }


}

