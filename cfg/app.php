<?php

use App\domain\ProductMonthlyCounter;
use App\repository\ProductRepository;
use App\domain\{CasualProcedure, Product};
use \App\command\NotFoundWrapper;
use App\repository\DBLayer;

return [
    'app.domain_path' => ['app/domain'],
    'app.domain_class' => Product::class,
    'app.dev_mode' => true,
    'app.dbLayer' => DBLayer::class,
    'app.subscribers' => [ProductRepository::class, ProductMonthlyCounter::class],
    'app.observables' => [CasualProcedure::class, Product::class, NotFoundWrapper::class],
    'app.procedure_map' => require_once 'cfg/procedure_map.php',
    'app.product_map' => require_once  'cfg/product_map.php'
];