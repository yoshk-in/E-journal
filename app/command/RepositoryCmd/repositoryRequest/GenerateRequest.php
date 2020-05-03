<?php


namespace App\command\RepositoryCmd\repositoryRequest;


use App\base\exceptions\WrongInputException;
use App\domain\AbstractProduct;
use Generator;

/**
 * Class GenProductRequester
 * @package App\command\DBRequestCmd\requester
 * @deprecated
 */
class GenerateRequest extends RepositoryRequest
{

    public function doGet(array $productData): Generator
    {
        exit('deprecated logic');
        $product = $this->productRepository->findLast();
        if (!is_null($product)) {
            $lastNumber = $product->nextMainNumber();
            if (is_null($lastNumber)) return;
            foreach ($this->productRepository->createProducts(range($lastNumber + 1, count($productData))) as $product) {
                yield $product;
            }
        }
    }


    public function removeFoundById(AbstractProduct $product, array &$collection): array
    {
        unset($collection[$product->getProductId()]);
        return $collection;
    }
}