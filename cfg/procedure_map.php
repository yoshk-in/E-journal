<?php

return [
    'Г9' => [                    //product name
        'настройка' =>                              //procedure name
            [
                'short' => 'настрой',            //short name
                'next' => 'прическа',             //next_state
            ],
        'техтренировка' =>                      //procedure name
            [
                'short' => 'тт',                    //short name
                'next' => 'механика ОТК',           //next_state
                'inners' =>
                    [                         //partial procedures
                        'вибропрочность' =>                         //procedure name
                            [
                                'short' => 'вибро',                //short name
                                'interval' => 'PT1S',                  //interval
                                'relax' => false
                            ],
                        'прогон' =>                                 //procedure name
                            [
                                'short' => 'прогон',               //short name
                                'interval' => 'PT1S',                  //interval
                                'relax' => false
                            ],
                        'морозоустойчивость' =>                     //procedure name
                            [
                                'short' => 'мороз',                //short name
                                'interval' => 'PT1S',                 //interval
                                'relax' => true,                   //required relax
                            ],
                        'теплоустойчивость' =>                  //procedure name
                            [
                                'short' => 'жара',                 //short name
                                'interval' => 'PT1S',                 //interval
                                'relax' => true                    //required relax
                            ],
                    ],
                'relax' => 'PT1S',               //interval for tt procedure relax
            ],
        'электрика ОТК' =>                      //procedure name
            [
                'short' => 'ОТК',                   //short name
                'next' => 'механика ПЗ',          //next_state
            ],
        'электрика ПЗ' =>                        //procedure name
            [
                'short' => 'ПЗ',                    //short name
                'next' => 'склад',                //next_state
            ],
    ],


    'НР381Б-02' => [
        'настройка' =>                          //procedure name
            [
                'short' => 'настрой',            //short name
                'next' => 'прическа',             //next_state
            ],
        'электрика ОТК' =>                      //procedure name
            [
            'short' => 'ОТК',                   //short name
            'next' => 'механика ПЗ',          //next_state
            $tt = 'inners' =>               //partial procedures
                [
                    'вибропрочность' =>                         //procedure name
                        [
                            'short' => 'вибро',                //short name
                            'interval' => 'PT1S',                  //interval
                            'relax' => false
                        ],
                    'прогон' => [                               //procedure name
                        'short' => 'прогон',                    //short name
                        'interval' => 'PT1S',                  //interval
                        'relax' => false
                    ],
                ],
        ],
        'электрика ПЗ' =>                       //procedure name
            [
            'short' => 'ПЗ',                    //short name
            'next' => 'склад',                //next_state
            $tt
        ],
    ]
];
