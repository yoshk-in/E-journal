<?php

namespace App\command;



use App\base\AppMsg;

class Info extends Informer
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    ) {
        $collection = $this->productRepository->findNotFinished($productName);
        if (!$collection->isEmpty()) {
            $this->dispatcher->update($collection, AppMsg::INFO);
        }
    }
}

