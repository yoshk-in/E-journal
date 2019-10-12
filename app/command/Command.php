<?php

namespace App\command;

use App\base\exceptions\WrongInputException;
use App\base\CLIRequest;
use App\cache\Cache;
use App\domain\ProcedureMap;
use App\repository\ProductRepository;


abstract class Command
{


    protected function ensureRightInput(bool $condition, string $msg = '', ?array $numbers = null)
    {
        $numb_str = '';
        if ($numbers) foreach ($numbers as $number) $numb_str .= $number . "\n";
        if (!$condition) throw new WrongInputException("неверно заданы параметры запроса: $msg\n $numb_str");
    }


}

