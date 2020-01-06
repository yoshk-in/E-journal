<?php


namespace App\command;


class ChangeProductNumber extends ByProductProperties
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $founds = $this->productRepository->findUnfinishedByAdvancedNumber($productName, array_keys($this->request->getChangingNumbers()));
        foreach ($founds as $product) {
            $product->setNumbers($this->request->getChangingNumbers()[$product->getAdvancedNumber()], $product->getAdvancedNumber());
        }
    }
}