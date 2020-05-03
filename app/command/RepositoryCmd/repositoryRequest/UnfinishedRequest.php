<?php


namespace App\command\RepositoryCmd\repositoryRequest;


use App\domain\AbstractProduct;
use App\command\RepositoryCmd\notFoundHandler\CommonNotFoundHandler;
use Generator;

class UnfinishedRequest extends RepositoryRequest
{

    public function doGet(array $productData): Generator
    {
        (self::REQUESTING_SUBJECT_DATA)::findNotEnded();
        foreach ($this->productRepository->find($productData) as $product) {
            yield $product;
        }
    }


    public function removeFoundById(AbstractProduct $product, array &$collection): array
    {
        unset($collection[$product->getProductId()]);
        return $collection;
    }
}