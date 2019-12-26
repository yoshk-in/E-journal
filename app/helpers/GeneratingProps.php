<?php


namespace App\helpers;


use phpDocumentor\Reflection\Types\Callable_;

class GeneratingProps
{
    public ?string $class;
    public ?\Closure $get;
    public ?\Closure $make;
    public ?array $inject;
    public ?array $scalar;
}