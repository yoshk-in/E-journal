<?php


namespace App\command;


class Dispatch extends Move
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    ) {
        [$found_products, $not_found] = $this->productRepository->findByNumbers($productName, $numbers);
        $this->ensureRightInput((bool)!$not_found, self::ERR_NOT_ARRIVED, $not_found);

        foreach ($found_products as $product) {
            $product->endProcedure();
        }

    }
}