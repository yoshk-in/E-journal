<?php


namespace App\helpers;



use Closure;

class DeferComputing
{

    private string $name;
    private Closure $deffer;

    public function __construct(string $name, Closure $deffer)
    {
        $this->name = $name;
        $this->deffer = $deffer;
    }


    public function compute($arg)
    {
        return ($this->deffer)($arg);
    }

    public function getName(): string
    {
        return $this->name;
    }
}