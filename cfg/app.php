<?php

use App\console\Render;
use App\domain\{Product, Procedure};
use App\parallel\ParallelExecution;


return [
    'app.domain_path' => ['app/domain'],
    'app.domain_class' => Product::class,
    'app.dev_mode' => true,
    'app.subscribers' => [Render::class, ParallelExecution::class],
    'app.observables' => [Procedure::class, Product::class],
    'app.procedure_map' => require_once 'cfg/procedure_map.php'
];