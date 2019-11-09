<?php


namespace App\command;


use App\base\AppMsg;
use App\domain\CompositeProcedure;

class CurrentProcInfo extends Move
{

    protected function doExecute(string $productName, array $numbers, ?string $procedure)
    {
        $products = $this->productRepository->findByNumbers($productName, $numbers);
        foreach ($products as $product) {
            $proc = $product->getCurrentProc();
            if ($proc instanceof CompositeProcedure) {
                $proc = $proc->getInnerByName($procedure);
            }
            $proc->notify(AppMsg::CURRENT_PROC_INFO);
        }

    }
}