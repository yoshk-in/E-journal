<?php


namespace App\domain\procedures\factories;


use App\domain\procedures\CasualProcedure;
use App\domain\procedures\CompositeProcedure;
use App\domain\procedures\traits\IProcedureOwner;
use Generator;

interface IProductProcedureFactory
{
    /**
     * @param IProcedureOwner $productOrProcedureOwner
     * @return Generator|CasualProcedure[]|CompositeProcedure[]
     */
    public function create(IProcedureOwner $productOrProcedureOwner): Generator;
}