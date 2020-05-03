<?php


namespace App\objectPrinter;


trait TPrintingObject
{
    public function __toString()
    {
        return AbstractPrinter::printAny($this);
    }
}