<?php

namespace App\command;


class SetPartNumberCommand extends Command
{

    protected function doExecute(
        $productName,
        $numbers,
        $procedure
    )
    {
        $partNumber = $this->request()->getPartNumber();
        $this->cache->setPartNumber($productName, $partNumber);
        echo $productName . ' установлен номер партии ' . $partNumber;
    }
}
