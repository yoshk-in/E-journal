<?php


namespace App\base;


use App\domain\Product;

class GUIRequest extends AbstractRequest
{
    protected $env = AppMsg::GUI;
    protected $blocks = [];
    protected $doubleNumberBlocks = [];


    public function addBlock(Product $product): void
    {
        $this->blocks[$product->getNumber()] = $product->getNumber();
    }

    public function addDoubleNumberBlock(Product $product)
    {
        $this->doubleNumberBlocks[$product->getAdvancedNumber()] = $product->getNumber();
    }

    public function removeDoubleNumberBlocks(Product $product)
    {
        $this->doubleNumberBlocks[$product->getAdvancedNumber()];
    }

    public function removeBlock(Product $product)
    {
        unset($this->blocks[$product->getNumber()]);
    }

    public function prepareReqByBufferNumbers()
    {
        $this->blockNumbers = $this->blocks;
        $this->doubleNumbers = $this->doubleNumberBlocks;
    }

    public function reset()
    {
        $this->commands = [];
        $this->blockNumbers = [];
        $this->blocks = [];
        $this->doubleNumberBlocks = [];
    }
}