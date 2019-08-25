<?php


namespace App\command;


use App\domain\ProductRepository;

class BlocksAreArrivedCommand extends Command
{
    protected function doExecute(
        \ArrayAccess $collection,
        ProductRepository $repository,
        string $domainClass,
        string $productName,
        array $not_found,
        ?string $procedure
    ) : array
    {
        foreach ($collection as $product) $output[] = $product->startProcedure($procedure);
        if (!empty($not_found)) {
            $this->ensureRightInput(is_null($procedure), 'данные блоки еше не поступали на настройку:', $not_found);
            $newCollection = $repository->createProducts($not_found, $domainClass, $productName);
            foreach ($newCollection as $product) $output[] = $product->startProcedure();
        }
        return $this->getCommonInfo($output ?? null);
    }

}