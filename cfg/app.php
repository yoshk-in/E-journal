<?php

use App\infoManager\InfoManager;
use App\repository\ProductRepository;
use App\domain\{CasualProcedure, Product};
use App\parallel\ParallelExecution;
use \App\command\NotFoundNumbersWrapper;

return [
    'app.domain_path' => ['app/domain'],
    'app.domain_class' => Product::class,
    'app.dev_mode' => false,
    'app.subscribers' => [InfoManager::class, ProductRepository::class],
    'app.observables' => [CasualProcedure::class, Product::class, NotFoundNumbersWrapper::class],
    'app.procedure_map' => require_once 'cfg/procedure_map.php'
];