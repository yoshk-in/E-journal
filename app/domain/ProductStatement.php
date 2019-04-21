<?php

namespace App\domain;

use DateTime;
use DateInterval;

class ProductStatement
{
    private $incoming;
    private $leaving;
    private $process;
    private $process_time;
    private $start_process;
    private $end_process;
    private $name;

    protected $statesArray = [
        'writeInBD' => 0,
        'prozvon' => 1,
        'nastroy' => 2,
        'vibro' => 3,
        'progon' => 4,
        'moroz' => 5,
        'jara' => 6,
        'mechanikaOTK' => 7,
        'electrikaOTK' => 8,
        'mechanikaPZ' => 9,
        'electrikaPZ' => 10,
        'sklad' => 11
    ];

    public function __construct($name)
    {
        $this->name = $name;
        $this->incoming     = new DateTime();
    }


    public function startProcess()
    {
        $this->start_process = new DateTime();
        $this->end_process   = $this->start_process->diff($this->process_time);
    }

    /**
     * @return mixed
     */
    public function getLeaving()
    {
        return $this->leaving;
    }

    /**
     * @param mixed $leaving
     */
    public function setLeaving($leaving): void
    {
        $this->leaving = $leaving;
    }

    /**
     * @return mixed
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param mixed $process
     */
    public function setProcess($process): void
    {
        $this->process = $process;
    }

    /**
     * @return mixed
     */
    public function getProcessTime()
    {
        return $this->process_time;
    }

    /**
     * @param mixed $process_time
     */
    public function setProcessTime($process_time): void
    {
        $this->process_time = $process_time;
    }

    /**
     * @return mixed
     */
    public function getStartProcess()
    {
        return $this->start_process;
    }

    /**
     * @param mixed $start_process
     */
    public function setStartProcess($start_process): void
    {
        $this->start_process = $start_process;
    }

    /**
     * @return mixed
     */
    public function getEndProcess()
    {
        return $this->end_process;
    }

    public function isFinished()
    {

    }


}

