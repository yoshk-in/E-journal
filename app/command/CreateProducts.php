<?php


namespace App\command;


use App\base\AppMsg;

class CreateProducts extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
       $products = $this->productRepository->createProducts($numbers, $productName);
        $this->productRepository->save();
        foreach ($products as $product) {
           $product->report(AppMsg::GUI_INFO . $product->getName());
       }
    }
}