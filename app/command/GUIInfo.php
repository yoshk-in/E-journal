<?php


namespace App\command;


use App\base\AppMsg;

class GUIInfo extends ByProductProperties
{
    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {

        $products = $this->productRepository->findUnfinished($productName);
        !empty($products) ?:(new NotFoundWrapper([]))->notify(AppMsg::NOT_FOUND);
        foreach ($products as $key => $product) {
            $product->report(AppMsg::GUI_INFO . $product->getName());
        }
        $this->productRepository->save();
    }


}