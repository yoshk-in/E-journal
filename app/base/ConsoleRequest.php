<?php

namespace App\base;

class ConsoleRequest extends AbstractRequest
{
    protected $commands = [];
    protected $blockNumbers = [];
    protected $partNumber;
    protected $partialProcName;
    protected $consoleArgs = [];
    protected $productName;


    public function __construct()
    {
        $this->setConsoleArgs();

    }



    public function getProductName()
    {
        return $this->productName;
    }


    public function setProductName($productName): void
    {
        $this->productName = $productName;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }


    public function addCommand(string $command): void
    {
        $this->commands[] = $command;
    }


    public function getBlockNumbers()
    {
        return $this->blockNumbers;
    }


    public function setBlockNumbers(array $blockNumbers): void
    {
        $this->blockNumbers = $blockNumbers;
    }


    public function getPartNumber()
    {
        return $this->partNumber;
    }


    public function setPartNumber($partNumber): void
    {
        $this->partNumber = $partNumber;
    }


    public function getPartialProcName()
    {
        return $this->partialProcName;
    }


    public function setPartialProcName(string $partialProcName): void
    {
        $this->partialProcName = $partialProcName;
    }


    public function getConsoleArgs(): array
    {
        return $this->consoleArgs;
    }

    public function setConsoleArgs(): void
    {
        $this->consoleArgs = $_SERVER['argv'];
    }



}

