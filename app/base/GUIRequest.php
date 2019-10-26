<?php


namespace App\base;


use App\domain\Product;

class GUIRequest extends AbstractRequest
{
    protected $env = AppMsg::GUI;
    protected $blocks;

    public function addBlockNumber(int $number)
    {
        $this->blockNumbers[] = $number;
    }
    public function addBlock(Product $block): void
    {
        $this->blocks[$block->getNumber()] = $block->getNumber();
    }

    public function removeBlock(Product $block)
    {
        unset($this->blocks[$block->getNumber()]);
    }

    public function setCmd(string $command = AppMsg::INFO)
    {
        $this->blockNumbers = array_values($this->blocks ?? []);
        $this->addCmd($command);

    }
}