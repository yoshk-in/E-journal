<?php

namespace App\command;


class FullInfoCommand extends Command
{
    protected function doExecute(
        $repository,
        $productName,
        ?array $numbers = null,
        ?string $procedure = null
    ) {
        $collection = $repository->findNotFinished($productName);
        if (!$collection->isEmpty()) {
            foreach ($collection as $product) {
                $product->notify();
            }
        }
    }
}

