<?php


namespace App\command;


use App\base\AppMsg;

class GUIInfo extends Move
{
    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $numbersMng = $this->productRepository->getNumbersMng($productName);
        $numberHorizon = $numbersMng->getHorizonNumber();
        if (empty($numberHorizon)) {
            $products = $this->productRepository->findAll($productName);
            if (empty($products)) {
                (new NotFoundWrapper([]))->notify(AppMsg::NOT_FOUND);
                return;
            }
            foreach ($products as $product) {
                $product->report(AppMsg::INFO);
            }

        }
    }
}