<?php


namespace App\command;


class BlocksAreArrivedCommand extends Command
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    )  {
        [$found_products, $not_found] = $this->productRepository->findByNumbers($productName, $numbers);
        if (!empty($not_found)) {
            $this->ensureRightInput(is_null($procedure), self::ERR['not_arrived'], $not_found);
            $new = $this->productRepository->createProducts($not_found, $productName);
        }
        $all = array_merge($found_products->toArray(), $new ?? []);

        foreach ($all as $product) $product->startProcedure($procedure);
    }
}