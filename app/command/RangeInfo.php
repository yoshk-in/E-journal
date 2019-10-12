<?php

namespace App\command;


use App\base\AppMsg;

class RangeInfo extends Informer
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    )
    {
        [$collection, $not_found] = $this->productRepository->findByNumbers($productName, $numbers);
        if (!$collection->isEmpty()) {
            $this->render->update($collection, AppMsg::RANGE_INFO);
        }
        if (!empty($not_found)) {
            $this->render->update($not_found, AppMsg::RANGE_INFO);
        }


    }

}

