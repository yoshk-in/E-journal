<?php

namespace App\command;



use App\base\AppMsg;

class Info extends Move
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    ) {
        $collection = $this->productRepository->findNotFinished($productName);
        if (!$collection->isEmpty()) {
            foreach ($collection as $product) {
                $product->report(AppMsg::INFO);
            }
        } else {
            echo 'блоков в работе на текущий момент нет' . PHP_EOL;
            exit;
        }
    }
}

