<?php


namespace App\command;


use App\base\AppMsg;
use App\events\Event;
use App\GUI\GUIManager;

class CreateProducts extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
       $products = $this->productRepository->createProducts($numbers, $productName);
       foreach ($products as $product) {
           $product->report(AppMsg::INFO);
       }
       $this->productRepository->save();
    }
}