<?php

use App\infoManager\CLIInfoManager;
use App\repository\ProductRepository;
use App\domain\{CasualProcedure, Product};
use \App\command\NotFoundWrapper;
use App\events\ProductTableSynchronizer;

return [
    'app.domain_path' => ['app/domain'],
    'app.domain_class' => Product::class,
    'app.dev_mode' => false,
    'app.subscribers' => [ProductRepository::class],
    'app.observables' => [CasualProcedure::class, Product::class, NotFoundWrapper::class],
    'app.procedure_map' => require_once 'cfg/procedure_map.php'
];