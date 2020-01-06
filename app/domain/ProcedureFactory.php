<?php


namespace App\domain;



class ProcedureFactory
{
    private ProcedureMap $creationMap;

    public function __construct(ProcedureMap $creationMap)
    {
        $this->creationMap = $creationMap;
    }

    public function createProcedures(Product $product): array
    {
        $orderNumber = 0;
        foreach ($this->creationMap->getProcedures($product->getName()) as $idState => $proc) {
            [$advanced, $class, $countPlus ] =
                ($parts = &$proc['inners'] ?? null) ?
                    [[$proc['next'], $parts, $this], CompositeProcedure::class, count($parts)]
                    :
                    [[], CasualProcedure::class, 0];

            $array[] = new $class($proc['name'], ++$orderNumber, $product, ...$advanced);
            $orderNumber += $countPlus;
        }
        return $array;
    }

    public function createPartials(array $partials, CompositeProcedure $owner): \Generator
    {
        $orderNumber = $owner->getOrderNumber();
        foreach ($partials as $partial) {
            yield new PartialProcedure($partial['name'],  ++$orderNumber, $owner, $partial['interval']);
        }
    }
}