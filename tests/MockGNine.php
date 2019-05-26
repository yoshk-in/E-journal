<?php


namespace tests;


use App\domaini\GNine;
use Doctrine\Common\Collections\ArrayCollection;

class MockGNine extends GNine
{
    protected $ttProcedureRules = [
        'vibro' => 'PT1S',
        'progon' => 'PT1S',
        'moroz' => 'PT1S',
        'jara' => 'PT1S'
    ];

    protected $relaxProcedure = [
        'climaticRelax' => 'PT1S'
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