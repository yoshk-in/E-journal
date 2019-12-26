<?php


namespace App\command;



class Forward extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        foreach ($this->request->getBlockDoubleNumbers() as $advanceNumber => $mainNumber) {
            is_null($mainNumber) ? $advanceNumbs[] = $advanceNumber : $numbers[] = $mainNumber;
//            $setNumbers[$advanceNumber] = $mainNumber;
        }
        if (empty($numbers) && empty($advanceNumbs)) throw new \Exception('numbers are required');

        empty($numbers) ?: $found_products = $this->productRepository->findByNumbers($productName, $numbers);
        empty($advanceNumbs) ?: $advancedFounds = $this->productRepository->findUnfinishedByAdvancedNumber($productName, $advanceNumbs);
        foreach (array_merge($found_products ?? [], $advancedFounds ?? []) as $product) {
            $product->forward();
        }
        $this->productRepository->save();
    }
}