<?php

return [
    'Г9' => [                    //product name
        [
            'name' => 'настройка',              //name
            'short' => 'настрой',            //ru name
            'next' => 'прическа',             //next_ru_state
        ],
        [
            'name' => 'техтренировка',      //name
            'short' => 'тт',          //ru name
            'next' => 'механика ОТК',           //next_ru_state
            'inners' =>
                [                         //partial procedures
                    [
                        'name' => 'вибропрочность',                //name
                        'short' => 'вибро',                //ru name
                        'interval' => 'PT1S',                  //interval
                        'relax' => false
                    ],
                    [
                        'name' => 'прогон',               //name
                        'short' => 'прогон',               //ru name
                        'interval' => 'PT1S',                  //interval
                        'relax' => false
                    ],
                    [
                        'name' => 'морозоустойчивость',                //name
                        'short' => 'мороз',                //ru name
                        'interval' => 'PT1S',                 //interval
                        'relax' => true,                   //required relax
                    ],
                    [
                        'name' => 'теплоустойчивость',                 //name
                        'short' => 'жара',                 //ru name
                        'interval' => 'PT1S',                 //interval
                        'relax' => true                    //required relax
                    ],
                ],
            'PT1S',                     //interval to relax
        ],
        [
            'name' => 'электрика ОТК',         //name
            'short' => 'ОТК',        //ru name
            'next' => 'механика ПЗ',          //next_ru_state
        ],
        [
            'name' => 'электрика ПЗ',          //name
            'short' => 'ПЗ',         //ru name
            'next' => 'склад',                //next_ru_state
        ],
    ]
];
