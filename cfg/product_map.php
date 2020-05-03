<?php


use App\domain\numberStrategy\SimpleNumberStrategy;
use App\domain\numberStrategy\DoubleNumberStrategy;



return [
    'Г9' => [
        'monthly countable' => true,
        'numberStrategy' => SimpleNumberStrategy::class,
        'mainNumber.length' => 6,
        'preNumber.length' => 6,
        'partNumber.length' => 3,

    ],
    'НР381Б-02' => [
        'monthly countable' => false,
        'numberStrategy' => DoubleNumberStrategy::class,
        'mainNumber.length' => 6,
        'preNumber.length' => 3,
        'partNumber.length' => 3,
    ]
];