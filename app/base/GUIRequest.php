<?php


namespace App\base;


use App\domain\AbstractProduct;

class GUIRequest extends AbstractRequest
{
    protected array $blocks = [];
    protected array $doubleNumberBlocks = [];


    public function addBlock(AbstractProduct $product): void
    {
        $this->blocks[$product->getProductNumber()] = $product->getProductNumber();
    }

    public function addDoubleNumberBlock(AbstractProduct $product)
    {
        $this->doubleNumberBlocks[$product->getPreNumber()] = $product->getProductNumber();
    }

    public function removeDoubleNumberBlocks(AbstractProduct $product)
    {
        $this->doubleNumberBlocks[$product->getPreNumber()];
    }

    public function removeBlock(AbstractProduct $product)
    {
        unset($this->blocks[$product->getProductNumber()]);
    }

    public function prepareReqByBufferNumbers()
    {
        $this->productNumbers = $this->blocks;
        $this->doubleNumbers = $this->doubleNumberBlocks;
    }

    public function reset()
    {
        $this->commands = [];
        $this->productNumbers = [];
        $this->blocks = [];
        $this->doubleNumberBlocks = [];
    }
}