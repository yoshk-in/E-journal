<?php


namespace App\domain\procedures\traits;


use App\domain\procedures\CasualProcedure;
use App\domain\procedures\interfaces\ProcedureInterface;
use Doctrine\Common\Collections\Collection;

interface IProcedureOwner
{

    function getProductId(): string;

    function getInnerProcedures(): array;

    function getInnerByName(string $name): ProcedureInterface;

    function procedureOwnerHandling(ProcedureInterface $procedure);

    function getInnerNotEndedProcedures(): array;

    function getInnerEndedProcedures(): array;
}