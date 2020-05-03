<?php

use App\domain\ProductMap;
use App\GUI\components\ProductSelectEmitter;
use Psr\Container\ContainerInterface;

return [
    ProductSelectEmitter::class => function (ContainerInterface $container) {
        $productMap = $container->get(ProductMap::class);
        return new ProductSelectEmitter($productMap->getProducts());
    }
];