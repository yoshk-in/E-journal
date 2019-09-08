<?php

use App\console\Render;
use App\domain\{Product, Procedure};


return [
    'app.domain_path' => ['app/domain'],
    'app.domain_class' => Product::class,
    'app.dev_mode' => true,
    'app.procedure_subscribers' => [Render::class],
    'app.observables' => [Procedure::class]
];