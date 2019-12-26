<?php


namespace App\command;


class ChangeProductMainNumber extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {

        $founds = $this->productRepository->findUnfinishedByAdvancedNumber($productName, array_keys($this->request->getChangingNumbers()));
        foreach ($founds as $product) {
            $product->setNumbers($this->request->getChangingNumbers()[$product->getAdvancedNumber()], $product->getAdvancedNumber());
        }
    }
}