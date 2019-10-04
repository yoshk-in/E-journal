<?php

namespace App\command;


use App\events\Event;

class FullInfoCommand extends RepositoryCommand
{
    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    ) {
        $collection = $this->productRepository->findNotFinished($productName);
        if (!$collection->isEmpty()) {
            foreach ($collection as $product) {
                $product->report(Event::UNFINISHED_PRODUCTS_INFO);
            }
        }
    }
}

