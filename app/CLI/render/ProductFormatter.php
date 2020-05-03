<?php


namespace App\CLI\render;


use App\base\exceptions\ObjectStateException;
use App\domain\AbstractProduct;
use Doctrine\Common\Collections\Collection;

class ProductFormatter extends Render
{
    const NUMBER = Format::PRODUCT_NUMBER . Format::EOL;


    function doHandle($processed)
    {
        $writeClass = AbstractProduct::class;
        /** @var AbstractProduct $processed */
        ($processed instanceof $writeClass) || ObjectStateException::create([
            "wrong processed class: $writeClass expected, " . get_class($processed) . "given'"
        ]);
        $this->result = sprintf(self::NUMBER, $processed->getNumber());
        return $processed->getEndedProcedures();
    }
}