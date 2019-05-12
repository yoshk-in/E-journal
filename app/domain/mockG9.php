<?php


namespace App\domain;


class mockG9 extends G9
{
    protected static $TECHNICAL_PROCEDURES_REGULATIONS = [
        'vibro' => 'PT1S',
        'progon' => 'PT5S',
        'moroz' => 'PT2S',
        'rest' => 'PT2S',
        'jara' => 'PT2S'
    ];

    protected static $PROCEDURES_REGULATIONS = [
        'minProcTime' => 'PT1S'
    ];
}