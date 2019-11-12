<?php


namespace App\command;


use App\base\AppMsg;

class GUIInfo extends Move
{
    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $products = $this->productRepository->findUnfinished($productName);
        if (empty($products)) {
            $product = $this->productRepository->findLast($productName);
            if (empty($product)) {
                (new NotFoundWrapper([]))->notify(AppMsg::NOT_FOUND);
                return;
            }
            $last = $product->getNumber();
            $products = $this->createNumbers($productName, range($last +1, $last + 11));

        }
//        $numberOrderReference = $products[0]->getNumber();
        foreach ($products as $key => $product) {
//            $this->extraCheckMissingNumbers($productName, $product, $numberOrderReference, $products, $key);
//            ++$numberOrderReference;
            $product->report(AppMsg::GUI_INFO);
        }
        $this->productRepository->save();
    }

//    protected function extraCheckMissingNumbers(string $productName, Product $product, int $nextNumber, array &$checkingArray, int $currentKey)
//    {
//        if ($product->getNumber() !== $nextNumber) {
//            $missingNumbers = [];
//            $this->checkMissingElemOfArray($checkingArray, ++$currentKey, $nextNumber, $missingNumbers);
//        } else {
//            return;
//        }
//        $products = $this->createNumbers($productName, $missingNumbers);
//        foreach ($products as $product) {
//            $product->report(AppMsg::GUI_INFO);
//        }
//
//    }
//
//    protected function checkMissingElemOfArray(array $array, int &$currentKey, int &$reference, array $store)
//    {
//        if (!isset($array[$currentKey])) return;
//        if ($array[$currentKey]->getNumber() === $reference) return;
//        $store[] = $reference;
//        $this->checkMissingElemOfArray($array, ++$currentKey, ++$reference, $store);
//        return $store;
//    }

    protected function createNumbers(string $productName, array $numbers): array
    {
        return $this->productRepository->createProducts($numbers, $productName);

    }
}