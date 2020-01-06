<?php


namespace App\domain\traits;


interface IIntervalProcedureOwner extends IProcedureOwner
{


    function processInnerEnd(TIntervalProcedure $procedure);
}