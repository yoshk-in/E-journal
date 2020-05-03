<?php

use App\domain\AbstractProduct;
use App\domain\procedures\AbstractProcedure;
use App\domain\procedures\executionStrategy\{AutoProcedureExecutionStrategy, ManualProcedureExecutionStrategy};
use App\domain\procedures\decorators\{ProcedureOwnerDecorator, ProductOwnerDecorator};
use App\domain\procedures\proxy\UpdateProcedureProxy;


/** @deprecated  */
return [
    AbstractProduct::class,
    AbstractProcedure::class,
    AutoProcedureExecutionStrategy::class,
    ManualProcedureExecutionStrategy::class,
    ProcedureOwnerDecorator::class,
    ProductOwnerDecorator::class,
    UpdateProcedureProxy::class,
];