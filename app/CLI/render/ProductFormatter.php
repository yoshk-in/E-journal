<?php


namespace App\CLI\render;


use App\domain\Product;

class ProductFormatter extends Formatter
{
    const NUMBER = Format::PRODUCT_NUMBER . Format::EOL;

    function doHandle($processed)
    {
        ($processed instanceof Product) || $this->exception();
        $this->buffer = sprintf(self::NUMBER, $processed->getNumber());
        return $processed->getProcedures();
    }
}