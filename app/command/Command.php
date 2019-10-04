<?php

namespace App\command;

use App\base\exceptions\WrongInputException;
use App\base\ConsoleRequest;
use App\cache\Cache;
use App\domain\ProcedureMapManager;
use App\repository\ProductRepository;


abstract class Command
{
    protected $request;



    public function __construct(ConsoleRequest $request)
    {
        $this->request = $request;
    }


    protected function request(): ConsoleRequest
    {
        return $this->request;
    }

    protected function ensureRightInput(bool $condition, string $msg = '', ?array $numbers = null)
    {
        $numb_str = '';
        if ($numbers) foreach ($numbers as $number) $numb_str .= $number . "\n";
        if (!$condition) throw new WrongInputException("неверно заданы параметры запроса: $msg\n $numb_str");
    }


}

