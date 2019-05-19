<?php


namespace tests;


use App\domaini\GNine;
use Doctrine\Common\Collections\ArrayCollection;

class MockGNine extends GNine
{
    protected $ttProcedureRules = [
        'vibro' => 'PT1S',
        'progon' => 'PT2S',
        'moroz' => 'PT2S',
        'jara' => 'PT2S'
    ];

    protected $relaxProcedure = [
        'climaticRelax' => 'PT2S'
    ];

    protected $proceduresRules = [
        'minTime' => 'PT1S'
    ];

    public function __clone()
    {
        $buffer = new ArrayCollection();
        foreach ($this->ttCollection as $proc) {
            $buffer->add(clone $proc);
        }
        $this->ttCollection = $buffer;
        $buffer = new ArrayCollection();
        foreach ($this->procsCollection as $proc) {
            $buffer->add(clone $proc);
        }
        $this->procsCollection = $buffer;
    }
}