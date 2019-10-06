<?php

namespace App\command;


class RangeInfoCommand extends InfoCommand
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    )
    {
        [$collection, $not_found] = $this->productRepository->findByNumbers($productName, $numbers);
        if (!$collection->isEmpty()) {
            $this->render->update($collection, __CLASS__);
        }
        if (!empty($not_found)) {
            $this->render->update($not_found, __CLASS__);
        }


    }

}

