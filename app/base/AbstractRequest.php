<?php


namespace App\base;


abstract class AbstractRequest
{

    protected $commands = [];
    protected $blockNumbers = [];
    protected $partNumber;
    protected $partialProcName;
    protected $productName;
    protected $env;
    protected $doubleNumbers = [];
    protected $changingNumbers = [];

    public function addChangingNumber($advancedNumber, $mainNumber)
    {
        $this->changingNumbers[$advancedNumber] = $mainNumber;
    }

    public function getChangingNumbers(): array
    {
        return $this->changingNumbers;
    }

    public function getEnv(): string
    {
        return $this->env;
    }

    public function getProduct(): string
    {
        return $this->productName;
    }


    public function setProduct($productName): void
    {
        $this->productName = $productName;
    }

    public function getCmd(): array
    {
        return $this->commands;
    }


    public function addCmd(string $command): void
    {
        $this->commands[$command] = $command;
    }


    public function getBlockNumbers(): array
    {
        return $this->blockNumbers;
    }

    public function getBlockDoubleNumbers(): array
    {
        return $this->doubleNumbers;
    }


    public function setBlockNumbers(?array $blockNumbers): void
    {
        $this->blockNumbers = $blockNumbers;
    }


    public function getParty()
    {
        return $this->partNumber;
    }


    public function setParty($partNumber): void
    {
        $this->partNumber = $partNumber;
    }


    public function getPartial()
    {
        return $this->partialProcName;
    }


    public function setPartial(?string $partialProcName): void
    {
        $this->partialProcName = $partialProcName;
    }

}