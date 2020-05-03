<?php


namespace App\domain\procedures\traits;


use App\domain\procedures\interfaces\ProcedureInterface;

trait TCasualProcedure
{
    public function getInnersCount(): int
    {
        return 0;
    }

    public function getProcessingOrNextProc(): ?ProcedureInterface
    {
        return null;
    }

    public function getNestingProcessedProc(): ProcedureInterface
    {
        return $this;
    }

    public function getFirstUnfinishedProcName(): ?string
    {
        return null;
    }

    public function getInnerByName(string $name): ?ProcedureInterface
    {
        return null;
    }

    public function getEndedProcedures(): array
    {
        return [];
    }

    public function getNotEndedProcedures(): array
    {
        return [];
    }

    public function innersEnded(): bool
    {
        return true;
    }

    public function getProcedures(): array
    {
        return [];
    }

    public function isComposite(): bool
    {
        return false;
    }

    public function getProcessingProc(): ?ProcedureInterface
    {
        return null;
    }



}