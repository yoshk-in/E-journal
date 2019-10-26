<?php


namespace App\GUI;


use App\base\GUIRequest;
use App\domain\Product;

class RequestMng extends GUIRequest
{
    private $request;
    private $blocks = [];

    public function __construct()
    {
        $this->request = new GUIRequest();
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param array $commands
     * @return RequestMng
     */
    public function setCommands(array $commands): RequestMng
    {
        $this->commands = $commands;
        return $this;
    }

    /**
     * @return array
     */
    public function getBlockNumbers(): array
    {
        return $this->blockNumbers;
    }

    public function addBlock(Product $block): void
    {
        $this->blocks[$block->getNumber()] = $block->getNumber();
    }

    public function removeBlock(Product $block)
    {
        unset($this->blocks[$block->getNumber()]);
    }


    public function getPartialProcName()
    {
        return $this->partialProcName;
    }

    /**
     * @param mixed $partialProcName
     * @return RequestMng
     */
    public function setPartialProcName($partialProcName)
    {
        $this->partialProcName = $partialProcName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param mixed $productName
     * @return RequestMng
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;
        return $this;
    }



}