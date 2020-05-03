<?php


namespace App\base\exceptions\traits;


use App\domain\AbstractProduct;
use App\domain\procedures\AbstractProcedure;
use App\domain\procedures\CasualProcedure;

class ProductProcedureMessageComposer
{
    private string $message = '';
    private array $messageArgs = [];

    public function fromProcedure(CasualProcedure $procedure)
    {
        $this->addArg($procedure->getName());
    }

    public function fromProduct(AbstractProduct $product)
    {
        $this->addArg($product->getProductName());
        $this->addArg($product->getProductNumber());
    }

    public function getArgs(): array
    {
        return $this->messageArgs;
    }


    public function addMessage(string $msg)
    {
        $this->message = $msg . $this->message;
    }

    public function getMessage(): string
    {
        $this->message = sprintf($this->message, $this->messageArgs);
        return $this->message;
    }

    protected function addArg($unit)
    {
        $this->messageArgs = [$unit, ...$this->messageArgs];
    }
}