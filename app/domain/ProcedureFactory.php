<?php


namespace App\domain;


class ProcedureFactory
{
    public static function createProcedures(array $procedures, Product $product): array
    {
        foreach ($procedures as $idState => $procedure) {
            $array[] = new Procedure($procedure['name'], $idState, $product, $procedure['composite']);
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