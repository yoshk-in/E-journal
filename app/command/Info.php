<?php

namespace App\command;



use App\base\AppMsg;
use Doctrine\Common\Collections\Collection;

class Info extends Move
{
    protected function doExecute($productName, $numbers, $procedure) {

        $products = $this->getNotFinished($productName);
        empty($products) ? $this->notFoundCase($productName) : $this->reportInfo($products);

    }

    protected function getNotFinished(string $productName): array
    {
        return $this->productRepository->findNotFinished($productName);
    }

    protected function reportInfo(array $collection)
    {
        foreach ($collection as $product) {
            $product->report(AppMsg::INFO);
        }
    }

    protected function notFoundCase(string $productName)
    {
        echo 'блоков в работе на текущий момент нет' . PHP_EOL;
        exit;
    }
}

