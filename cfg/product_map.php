<?php

use \App\domain\CasualNumberStrategy;
use App\domain\DoubleNumberStrategy;

return [
    'Г9' => [
        'monthly countable' => true,
        'numberStrategy' => CasualNumberStrategy::class,
        'mainNumberLength' => 6
    ],
    'НР381Б-02' => [
        'monthly countable' => false,
        'numberStrategy' => DoubleNumberStrategy::class,
        'mainNumberLength' => 6
    ]
];