<?php


namespace App\CLI\render;


use App\domain\AbstractProcedure;

interface IFormatter
{
    public function handle(AbstractProcedure $processed): string ;
}