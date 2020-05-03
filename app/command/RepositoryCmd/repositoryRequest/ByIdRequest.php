<?php


namespace App\command\RepositoryCmd\repositoryRequest;


use App\domain\AbstractProduct;
use Generator;

class ByIdRequest extends RepositoryRequest
{

    public function doGet($productData): Generator
    {
        foreach ($this->productRepository->findByUniqueProperties($productData) as $product) {
            yield from $product;
        }
    }




}