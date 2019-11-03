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
        foreach ($collection as $product) {
            $product->report(AppMsg::RANGE_INFO);
        }
        (new NotFoundNumbersWrapper($not_found))->notify(AppMsg::NOT_FOUND);

    }

}

