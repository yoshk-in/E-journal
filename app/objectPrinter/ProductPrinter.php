<?php


namespace App\objectPrinter;


use App\domain\AbstractProduct;
use App\domain\procedures\interfaces\NameStateInterface;

class ProductPrinter extends AbstractPrinter
{
    const PRINTING_PATTERN = ' Блок %s, номер %s ';

    /**
     * @param NameStateInterface | AbstractProduct $object
     * @return array
     */
    public static function getObjectProperties(NameStateInterface $object): array
    {
        $collection = parent::getObjectProperties($object);
        $collection[] = $object->getAnyNumber();
        return $collection;
    }

}