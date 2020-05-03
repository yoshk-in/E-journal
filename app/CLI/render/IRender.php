<?php


namespace App\CLI\render;


use App\domain\procedures\CasualProcedure;

interface IRender
{
    public function handle(CasualProcedure $processed): string ;
}