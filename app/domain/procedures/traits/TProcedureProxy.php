<?php


namespace App\domain\procedures\traits;


use App\domain\AbstractProduct;

trait TProcedureProxy
{

    protected function delegate($toObject, string $functionName,array $arguments)
    {
        return $toObject->$functionName(...$arguments);
    }

    public function getName(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function start(?string $innerName)
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    public function end(?string $innerName)
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    public function getState(): int
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getMark(): ?int
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getOwnerOrder(): int
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getProductOrder(): int
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getProduct(): AbstractProduct
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    function getStart(): ?\DateTimeInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    function getEnd(): ?\DateTimeInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getStateName(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isEnded(): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isStarted(): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

}