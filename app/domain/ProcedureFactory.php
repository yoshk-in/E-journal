<?php


namespace App\domain;


class ProcedureFactory
{
    private $creationMap;

    public function __construct(ProcedureMap $creationMap)
    {
        $this->creationMap = $creationMap;
    }

    public function createProcedures(Product $product): array
    {
        foreach ($this->creationMap->getProdProcArr($product->getName()) as $idState => $procedure) {
            $idState += $compositeInnersCount ?? 0;
            switch (isset($procedure['inners'])) {
                case true :
                    $composite = new CompositeProcedure($procedure['name'], $idState, $product, $procedure['next'], $procedure['inners']);
                    $compositeInnersCount = $composite->getInnersCount();
                    $array[] = $composite;
                    break;
                case false:
                    $array[] = new CasualProcedure($procedure['name'], $idState, $product, $procedure['next']);
            }
        }
        return $array;
    }

    public static function createPartials(array $partials, CompositeProcedure $owner): array
    {
        $idOwner = $owner->getIdState();
        foreach ($partials as $idState => $partial) {
            $array[] = new PartialProcedure($partial['name'], $idOwner + $idState + 1, $owner, $partial['interval']);
        }
        return $array;
    }
}