<?php

namespace App\command;

use \App\base\ConsoleRequest;
use \App\base\AppHelper;


class SetPartNumberCommand extends Command
{
    protected function doExecute(
        \ArrayAccess $collection,
        $repository,
        $domainClass,
        $productName,
        ?array $not_found = null,
        ?string $procedure = null
    ) : array
    {
        $partNumber = $this->request()->getPartNumber();
        $cache = AppHelper::init()->getCacheObject();
        $cache->setPartNumber($productName, $partNumber);
        return [$productName, 'установлен номер партии ' . $partNumber];
    }
}
