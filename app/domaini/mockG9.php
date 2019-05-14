<?php


namespace App\domaini;

/**
 * Class mockG9
 * needed for phpUnit tests
 */

class mockG9
{
    protected static $TECHNICAL_PROCEDURES_REGULATIONS = [
        'vibro' => 'PT1S',
        'progon' => 'PT5S',
        'moroz' => 'PT2S',
        'jara' => 'PT2S'
    ];

    protected static $PROCEDURES_REGULATIONS = [
        'minProcTime' => 'PT31S'
    ];

    protected static $RELAX_PROCEDURE = [
        'climatic_relax' => 'PT2S'
    ];
}