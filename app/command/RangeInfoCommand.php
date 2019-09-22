<?php

namespace App\command;


use Doctrine\Common\Collections\Collection;

class RangeInfoCommand extends Command
{
    protected function doExecute(
        $repository,
        $productName,
        ?array $numbers = null,
        ?string $procedure = null
    )
    {
        [$collection, $not_found] = $repository->findByNumbers($productName, $numbers);
        if (!$collection->isEmpty()) {
            foreach ($collection as $product) {
                $product->notify();
            }
        }
        if (!empty($not_found)) {
            echo 'по данным номерам информации не найдено: ';
            foreach ($not_found as $number) echo $number . ', ';
        }

    }

}

