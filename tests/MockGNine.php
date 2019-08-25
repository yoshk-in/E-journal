<?php


namespace tests;


use App\domain\GNine;
use Doctrine\Common\Collections\ArrayCollection;

class MockGNine extends GNine
{

    protected $relaxProcedure = [
        'climaticRelax' => 'PT1S'
    ];

    protected $proceduresRules = [
        'minTime' => 'PT1S'
    ];

    protected $time = 'PT1S';

    public function __construct()
    {
        parent::__construct();
        if ($this->time) {
            foreach ($this->getTTProcedureList() as $key => $rule) {
                self::$ttProcedureRules[$key][] = $this->time;
                self::$ttProcedureRules[$key][] = $rule[1];
            }
            $this->relaxProcedure['climaticRelax'] = $this->time;
            $this->proceduresRules['minTime'] = $this->time;
        }

    }

    public function __clone()
    {
        $buffer = new ArrayCollection();
        foreach ($this->ttCollection as $proc) {
            $buffer->add(clone $proc);
        }
        $this->ttCollection = $buffer;
        $buffer = new ArrayCollection();
        foreach ($this->procCollection as $proc) {
            $buffer->add(clone $proc);
        }
        $this->procCollection = $buffer;
    }
}