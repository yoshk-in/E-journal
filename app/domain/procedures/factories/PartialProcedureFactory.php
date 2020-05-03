<?php


namespace App\domain\procedures\factories;


use App\domain\procedures\CompositeProcedure;
use App\domain\procedures\traits\IProcedureOwner;

class PartialProcedureFactory extends ProcedureFactory
{
    /**
     * @param IProcedureOwner|CompositeProcedure $composite
     * @return array
     */
    protected function getProcedures(IProcedureOwner $composite): array
    {
        return $this->creationMap->getPartials($composite->getName());
    }



}