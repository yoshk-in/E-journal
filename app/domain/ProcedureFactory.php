<?php


namespace App\domain;


class ProcedureFactory
{
    private $creationMap;

    public function __construct(ProcedureMapManager $creationMap)
    {
        $this->creationMap = $creationMap;
    }

    public function createProcedures(Product $product): array
    {
        foreach ($this->creationMap[$product->getName()] as $idState => $procedure) {
            switch (isset($procedure['inners'])) {
                case true :
                    $array[] = new CompositeProcedure($procedure['name'], $idState, $product, $procedure['inners']);
                case false:
                    $array[] = new CasualProcedure($procedure['name'], $idState, $product);
            }
        }
        return $array;
    }

    public static function createPartials(array $partials, Procedure $owner): array
    {
        foreach ($partials as $idState => $partial) {
            $array[] = new PartialProcedure($partial['name'], $idState, $owner, $partial['interval']);
        }
        return $array;
    }
}