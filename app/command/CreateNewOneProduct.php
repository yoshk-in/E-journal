<?php


namespace App\command;


use App\base\AppMsg;

class CreateNewOneProduct extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $nextNumber = $this->productRepository->findFirstUnfinished($productName)->getNumber() + 1;
        $newProduct = null;
        while (empty($newProduct)) {
            ++$nextNumber;
            $newProduct = $this->productRepository->createProducts([$nextNumber], $productName);
        }
        $this->productRepository->save();
        $newProduct[0]->notify(AppMsg::GUI_INFO);
    }
}