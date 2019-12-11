<?php


namespace App\command;


use App\base\AppMsg;
use App\base\exceptions\WrongInputException;

class CreateNewOneProduct extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $last = $this->productRepository->findLast($productName);
        if (is_null($last) && empty($numbers)) throw new WrongInputException('введите номер');
        $nextNumber = $last ? $last->nextNumber() : $numbers[0];
        $newProduct = $this->productRepository->createProducts([$nextNumber], $productName);

        $this->productRepository->save();
        $newProduct[0]->notify(AppMsg::GUI_INFO . $newProduct->getName());
    }
}