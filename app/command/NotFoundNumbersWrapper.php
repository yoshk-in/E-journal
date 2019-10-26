<?php


namespace App\command;


use App\events\IObservable;
use App\events\TObservable;

class NotFoundNumbersWrapper implements IObservable
{
    use TObservable;

    private $numbers;

    public function __construct(array $numbers)
    {
        $this->numbers = $numbers;
    }

    public function getNumbers()
    {
        return $this->numbers;
    }
}