<?php

namespace App\command;

use App\base\AbstractRequest;
use App\base\exceptions\WrongInputException;


abstract class Command
{
    protected $request;

    public function __construct(AbstractRequest $request)
    {
        $this->request = $request;
    }

    protected function ensureRightInput(bool $condition, string $msg = '', ?array $numbers = null)
    {
        $numb_str = '';
        if ($numbers) foreach ($numbers as $number) $numb_str .= $number . "\n";
        if (!$condition) throw new WrongInputException("неверно заданы параметры запроса: $msg\n $numb_str");
    }


}

