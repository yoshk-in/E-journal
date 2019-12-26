<?php


namespace App\command;


use App\base\AppMsg;
use App\base\exceptions\WrongInputException;

class CreateProductOrGenerate extends Move
{
    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        if (empty($numbers)) {
            $last = $this->productRepository->findLast($productName);
            if (is_null($last) || $nextNumber =  $last->nextNumber()) throw new WrongInputException('введите номер');
        } else {
            $nextNumber = $numbers[0];
            $this->doubleNumberProduct($productName, $nextNumber);
        }
        $newProduct = $this->productRepository->createProducts([$nextNumber], $productName)[0];
        $this->productRepository->save();
        $newProduct->notify(AppMsg::GUI_INFO . $newProduct->getName());

    }

    protected function doubleNumberProduct($productName, int $clientNumber)
    {
        if (!$this->productMap->isDoubleNumbering($productName)) return;
        $repeatNumber = $this->productRepository->findUnfinishedByAdvancedNumber($productName, $clientNumber);
        if (!empty($repeatNumber)) throw new WrongInputException(' данный номер уже записан');
    }
}