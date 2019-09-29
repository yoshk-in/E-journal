<?php

namespace App\command;


use App\events\Event;

class RangeInfoCommand extends Command
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
                $product->report(Event::RANGE_PRODUCTS_INFO);
            }
        }
        if (!empty($not_found)) {
            echo 'по данным номерам информации не найдено: ';
            foreach ($not_found as $number) echo $number . ', ';
        }
        echo PHP_EOL;

    }

}

