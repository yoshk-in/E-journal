<?php


namespace tests;


use App\domaini\GNine;

class MockGNine extends GNine
{
    protected static $ttProcedureRules = [
        'vibro' => 'PT1S',
        'progon' => 'PT2S',
        'moroz' => 'PT2S',
        'jara' => 'PT2S'
    ];

    protected static $relaxProcedure = [
        'climatic_relax' => 'PT2S'
    ];

    protected static $proceduresRules = [
        'minProcTime' => 'PT1S'
    ];
}