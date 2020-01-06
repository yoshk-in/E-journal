<?php

namespace App\command\DBFindNumbers;


use App\base\AppMsg;
use App\domain\Product;
use App\command\NotFoundWrapper;

class RangeInfo extends FindNumbersCmd
{


    protected function doWithFound(Product $product, ?string $procedure = null)
    {
        $product->report(AppMsg::RANGE_INFO);

    }

    protected function doWithNotFounds(array $not_founds, $procedure)
    {
        (new NotFoundWrapper($not_founds))->report();
    }
}

