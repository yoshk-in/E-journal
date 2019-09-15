<?php

use App\console\Render;
use App\domain\{Product, Procedure};


return [
    'app.domain_path' => ['app/domain'],
    'app.domain_class' => Product::class,
    'app.dev_mode' => true,
    'app.subscribers' => [Render::class],
    'app.observables' => [Procedure::class, Product::class],
    'app.procedure_map' => require_once 'bootstrap/cfg/procedure_map.php'
];