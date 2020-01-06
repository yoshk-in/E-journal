<?php

namespace App\base;

class CLIRequest extends AbstractRequest
{
    protected int $partNumber;
    protected string $moveProductMethod;
    protected array $consoleArgs = [];

    public function __construct()
    {
        $this->setCLIArgs();
    }


    public function getCLIArgs(): array
    {
        return $this->consoleArgs;
    }

    public function setCLIArgs(): void
    {
        $this->consoleArgs = $_SERVER['argv'];
    }

    public function getParty()
    {
        return $this->partNumber;
    }

    public function setMoveProductMethod(string $move)
    {
        $this->moveProductMethod = $move;
    }

    public function getMoveProductMethod()
    {
        return $this->moveProductMethod;
    }

}

