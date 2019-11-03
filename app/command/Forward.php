<?php


namespace App\command;


use App\domain\AbstractProcedure;
use App\domain\CasualProcedure;
use App\domain\CompositeProcedure;
use App\GUI\GUIManager;


class Forward extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        [$found_products,] = $this->productRepository->findByNumbers($productName, $numbers);
        $all = array_merge($found_products->toArray(), $new ?? []);

        foreach ($all as $product) {
            $product->forward();
        }
        $this->productRepository->save();
    }
}