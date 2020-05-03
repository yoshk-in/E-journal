<?php


namespace App\command\RepositoryCmd\repositoryRequest;


use App\domain\AbstractProduct;
use Generator;

class CreateRequest extends RepositoryRequest
{

    public function doGet(array $productData): Generator
    {
        yield from $this->productRepository->createProducts($productData);
    }

}