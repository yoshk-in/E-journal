<?php


namespace App\domain;



trait DoctrineProcedureLifeCycleCallbacks
{
    /** @PostLoad */
    function emptyInnerProcCollToNull()
    {
        !$this->innerProcs->isEmpty() ?: $this->innerProcs = null;
    }
}