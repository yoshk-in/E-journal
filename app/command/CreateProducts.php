<?php


namespace App\command;


use App\base\AppMsg;

class CreateProducts extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
       $products = $this->productRepository->createProducts($numbers, $productName);
       foreach ($products as $product) {
           $product->report(AppMsg::GUI_INFO);
       }
       $this->productRepository->save();
    }
}