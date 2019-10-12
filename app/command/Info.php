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
            $this->render->update($collection, AppMsg::INFO);
        }
    }
}

