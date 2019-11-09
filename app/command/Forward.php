<?php


namespace App\command;



class Forward extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        [$found_products,] = $this->productRepository->findByNumbers($productName, $numbers);

        foreach ($found_products as $product) {
            $product->isFinished()? : $product->forward();
        }
        $this->productRepository->save();
    }
}