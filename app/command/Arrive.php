<?php


namespace App\command;


class  Arrive extends Move
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    )
    {
        [$found_products, $not_found] = $this->productRepository->findByNumbers($productName, $numbers);
        if (!empty($not_found)) {
            $this->ensureRightInput(is_null($procedure), self::ERR_NOT_ARRIVED, $not_found);
            $new = $this->productRepository->createProducts($not_found, $productName);
        }
        $all = array_merge($found_products, $new ?? []);
        foreach ($all as $product)  {
            $product->startProcedure($procedure);
        }
        $this->productRepository->save();

    }
}