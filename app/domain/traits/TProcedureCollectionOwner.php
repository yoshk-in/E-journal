<?php


namespace App\domain\traits;


use App\domain\procedures\interfaces\ProcedureInterface;

trait TProcedureCollectionOwner
{
    public function getInnersCount(): int
    {
        return $this->innerProcedures->count();
    }

    public function getProcessingOrNextProc(): ?ProcedureInterface
    {
        return $this->getProcessingProc() ?? $this->getFirstNotEnded();
    }

    public function getInnerByName(string $name): ProcedureInterface
    {
        return $this->innerProcedures[$name] ?? $this->analyze();
    }

    public function getInnerEndedProcedures(): array
    {
        return $this->finishedInners->toArray();
    }

    public function getInnerNotEndedProcedures(): array
    {
        return $this->notFinishedInners->toArray();
    }

    public function innersEnded(): bool
    {
        return $this->finishedInners->empty();
    }

    public function getInnerProcedures(): array
    {
        return $this->innerProcedures->toArray();
    }

    public function getFirstNotEnded(): ?ProcedureInterface
    {
        return $this->notFinishedInners->first();
    }


    public function getProcessingProc(): ?ProcedureInterface
    {
        return $this->processingInner;
    }


    public function getLastEnded(): ?ProcedureInterface
    {
        return $this->notFinishedInners->last();
    }

}