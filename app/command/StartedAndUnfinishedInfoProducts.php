<?php


namespace App\command;


use App\base\AppMsg;

class StartedAndUnfinishedInfoProducts extends ByProductProperties
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $products = $this->productRepository->findStartedAndUnfinished($productName);
        foreach ($products as $product) {
            $product->report(AppMsg::STAT_INFO);
        }
    }
}