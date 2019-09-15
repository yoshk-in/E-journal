<?php

namespace App\command;



use App\domain\Procedure;
use Doctrine\Common\Collections\Collection;

class RangeInfoCommand extends Command
{
    protected function doExecute(
        \ArrayAccess $collection,
        $repository,
        $domainClass,
        $productName,
        ?array $not_found = null,
        ?string $procedure = null
    )
    {
        foreach ($collection as $product) {
            $output[$productName] = $product->getInfo();
        }
        return [
            "информация по найденным блокам: \n" => $output,
            "о данных номерах нет записей в журнале:\n" => $not_found
        ];

    }

}

