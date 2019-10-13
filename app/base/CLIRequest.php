<?php

namespace App\base;

class CLIRequest extends AbstractRequest
{
    protected $env = AppMsg::CLI;

    public function __construct()
    {
        $this->setCLIArgs();
    }

    protected $consoleArgs = [];

    public function getCLIArgs(): array
    {
        return $this->consoleArgs;
    }

    public function setCLIArgs(): void
    {
        $this->consoleArgs = $_SERVER['argv'];
    }



}

