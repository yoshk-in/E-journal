<?php

namespace App\command;


use App\base\AppMsg;

class RangeInfo extends Move
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    )
    {
        [$collection, $not_found] = $this->productRepository->findByNumbers($productName, $numbers);
        if (!$collection->isEmpty()) {
            foreach ($collection as $product) {
                $product->report(AppMsg::RANGE_INFO);
            }
        }
        if (!empty($not_found)) {
            (new NotFoundNumbersWrapper($not_found))->notify('NotFoundNumbers');

        }


    }

}

