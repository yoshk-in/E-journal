<?php

namespace App\domain;


class ProcedureMap
{
    private const PROCEDURE_LIST = [
        'Г9' => [                    //product name
            [
                'name' => 'настройка',              //name
                'ru_name' => 'настрой',            //ru name
                'next_ru_state' => 'прическа',             //next_ru_state
                'composite' => null                  //is composite procedure
            ],
            [
                'name' => 'техтренировка',      //name
                'ru_name' => 'тт',          //ru name
                'next_ru_state' => 'механика ОТК',           //next_ru_state
                'composite' =>
                    [                         //partial procedures
                        [
                            'name' => 'вибропрочность',                //name
                            'ru_name' => 'вибро',                //ru name
                            'interval' => 'PT1S',                  //interval
                            'relax' => false
                        ],
                        [
                            'name' => 'прогон',               //name
                            'ru_name' => 'прогон',               //ru name
                            'interval' => 'PT1S',                  //interval
                            'relax' => false
                        ],
                        [
                            'name' => 'морозоустойчивость',                //name
                            'ru_name' => 'мороз',                //ru name
                            'interval' => 'PT1S',                 //interval
                            'relax' => true,                   //required relax
                        ],
                        [
                            'name' => 'теплоустойчивость',                 //name
                            'ru_name' => 'жара',                 //ru name
                            'interval' => 'PT1S',                 //interval
                            'relax' => true                    //required relax
                        ],
                    ],
                'PT1S',                     //interval to relax
            ],
            [
                'name' => 'электрика ОТК',         //name
                'ru_name' => 'ОТК',        //ru name
                'next_ru_state' => 'механика ПЗ',          //next_ru_state
                'composite' => null                                        //is composite procedure
            ],
            [
                'name' => 'электрика ПЗ',          //name
                'ru_name' => 'ПЗ',         //ru name
                'next_ru_state' => 'склад',                //next_ru_state
                'composite' => null                                           //is composite procedure
            ],
        ]
    ];

    /**
     * ProcedureMap constructor.
     */
    public function __construct()
    {
    }

    public function getProcedureList(): array
    {
        return self::PROCEDURE_LIST;
    }

    public function getProductNames(): array
    {
        return array_keys(self::PROCEDURE_LIST);
    }

    public function getProcedures(string $product): array
    {
        return self::PROCEDURE_LIST[$product];
    }

    public function getProcedureNames(string $product): array
    {
        foreach ($this->getProcedures($product) as $proc) {
            $names[] = $proc['name'];
        }
        return $names;
    }

    public function getPartials(string $product, string $procedure): array
    {
        $procedures = self::PROCEDURE_LIST[$product];
        foreach ($procedures as $key => $proc) {
            $found = ($proc['name'] !== $procedure) ?: $key;
            break;
        }
        return $procedures[$found]['composite'];
    }

    public function getPartialNames(string $product, ?string $procedure, ?string $ru): array
    {
        foreach ($this->getPartials($product, $procedure) as $partial) {
            $names[] = ($ru == 'ru') ? $partial['ru_name'] : $partial['name'];
        }
        return $names;
    }

    public function getAllPartialNames(string $product, ?string $ru = null) : array
    {
        $names = [];
        foreach ($this->getProcedures($product) as $proc) {
            !$proc['composite'] OR $names = array_merge($this->getPartialNames(
                $product, $proc['name'], $ru
            ), $names);

        }
        return $names;
    }

    public function getAllDoublePartialNames(string $product) : array
    {
        $shortNames = $this->getAllPartialNames($product, 'ru');
        $fullNames = $this->getAllPartialNames($product);
        foreach ($shortNames as $key => $name) {
            $result[] = [$name, $fullNames[$key]];
        }
        return $result;
    }
}

