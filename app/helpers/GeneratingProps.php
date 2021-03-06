<?php


namespace App\helpers;



use Closure;

class GeneratingProps
{
    public ?string $class = null;
    public ?Closure $get = null;
    public ?Closure $make = null;
    public array $inject = [];
    public array $scalar = [];
}