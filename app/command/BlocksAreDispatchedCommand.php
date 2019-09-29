<?php


namespace App\command;


class BlocksAreDispatchedCommand extends Command
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    ) {
        [$found_products, $not_found] = $this->productRepository->findByNumbers($productName, $numbers);
        $this->ensureRightInput((bool)!$not_found, self::ERR['not_arrived'], $not_found);

        foreach ($found_products as $product) {
            $product->endProcedure();
        }

    }
}