<?php


namespace App\command\DBFindNumbers;


use App\command\ByProductProperties;
use App\domain\Product;

abstract class FindNumbersCmd extends ByProductProperties
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $founds = $this->productRepository->findByNumbers($productName, $numbers);
        $not_founds = array_flip($numbers);
        foreach ($founds as $product) {
            $this->doWithFound($product, $procedure);
            unset($not_founds[$product->getNumber()]);
        }
        if (!empty($not_founds)) $this->doWithNotFounds($not_founds, $procedure);
    }

    abstract protected function doWithFound(Product $found, ?string $procedure = null);

    abstract protected function doWithNotFounds(array $not_founds, $procedure);


}