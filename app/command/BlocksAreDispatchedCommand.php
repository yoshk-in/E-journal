<?php


namespace App\command;


use App\domain\ProductRepository;

class BlocksAreDispatchedCommand extends Command
{
    protected function doExecute(
        ProductRepository$repository,
        string $productName,
        array $numbers,
        ?string $procedure = null
    ) {
        [$found_products, $not_found] = $this->productRepository->findByNumbers($productName, $numbers);
        $this->ensureRightInput((bool)!$not_found, self::ERR['not_arrived'], $not_found);

        foreach ($found_products as $product) $product->endProcedure();

    }
}