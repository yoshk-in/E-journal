<?php


namespace App\domain\traits;


interface IBeforeEndProcedure
{
    public function beforeEnd(): \DateInterval;
}