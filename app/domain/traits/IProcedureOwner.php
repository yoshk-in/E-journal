<?php


namespace App\domain\traits;


use App\domain\AbstractProcedure;
use Doctrine\Common\Collections\Collection;

interface IProcedureOwner
{
    function nextProcStart(AbstractProcedure $proc);

    function getProcedures(): Collection;

    function getInnerByName(string $name): AbstractProcedure;
}