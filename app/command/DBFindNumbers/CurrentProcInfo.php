<?php


namespace App\command\DBFindNumbers;


use App\base\AppMsg;
use App\domain\Product;

class CurrentProcInfo extends FindNumbersCmd
{

    protected function doWithFound(Product $product, ?string $procedure = null)
    {
        $proc = $product->getCurrentProcessedProc($procedure);
        $proc->notify(AppMsg::CURRENT_PROCEDURE_INFO);

    }

    protected function doWithNotFounds(array $not_founds, $procedure)
    {
        (new NotFoundWrapper($not_founds))->report();
    }
}