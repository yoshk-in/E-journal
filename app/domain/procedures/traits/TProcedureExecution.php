<?php


namespace App\domain\procedures\traits;



trait TProcedureExecution
{
    public function needsToUpdate(): bool
    {
        return $this->executionStrategy->needsToUpdate();
    }


}