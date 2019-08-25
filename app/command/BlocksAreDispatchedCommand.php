<?php


namespace App\command;


class BlocksAreDispatchedCommand extends Command
{
    protected function doExecute(
        \ArrayAccess $collection,
        $repository,
        $domainClass,
        $productName,
        ?array $not_found = null,
        ?string $procedure = null
    ): array
    {
        $this->ensureRightInput(
            (bool)!$not_found,
            ' данные блоки еще не поступали на прозвону(или настройку) ',
            $not_found
        );
        foreach ($collection as $product) $output[] = $product->endProcedure();
        return $this->getCommonInfo($output ?? null);
    }
}