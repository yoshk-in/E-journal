<?php

namespace App\command;



class FullInfoCommand extends InfoCommand
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    ) {
        $collection = $this->productRepository->findNotFinished($productName);
        if (!$collection->isEmpty()) {
            $this->render->update($collection, __CLASS__);
        }
    }
}

