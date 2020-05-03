<?php


namespace App\base\exceptions;


class ExceptionStubClass
{
    private string $msg;


    public function __construct($msg = "call method on null")
    {
        echo $msg . PHP_EOL;
    }

    public function __call($name, $arguments)
    {
        return new ExceptionStubClass();
    }

    public function __toString()
    {
        return '?';
    }

}