<?php

use App\CLI\render\InfoManager;
use App\domain\{Procedure, Product};
use App\parallel\ParallelExecution;


return [
    'app.domain_path' => ['app/domain'],
    'app.domain_class' => Product::class,
    'app.dev_mode' => true,
    'app.subscribers' => [InfoManager::class, ParallelExecution::class],
    'app.observables' => [Procedure::class, Product::class],
    'app.procedure_map' => require_once 'cfg/procedure_map.php'
];