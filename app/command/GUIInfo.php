<?php


namespace App\command;


use App\base\AppMsg;

class GUIInfo extends Move
{
    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $products = $this->productRepository->findUnfinished($productName);
        if (empty($products)) {
//            $product = $this->productRepository->findLast($productName);
//            if (empty($product)) {
                (new NotFoundWrapper([]))->notify(AppMsg::NOT_FOUND);
//                return;
//            }
//            $products = $this->createNumbers($productName, [$product->nextNumber()]);

        }
        foreach ($products as $key => $product) {
            $product->report(AppMsg::GUI_INFO . $product->getName());
        }
        $this->productRepository->save();
    }


}