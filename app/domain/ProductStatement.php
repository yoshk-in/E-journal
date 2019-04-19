<?php

namespace App\domain;

class ProductStatement
{
    private $incoming;

    private $leaving;

    private $process;

    private $process_time;

    private $start_process;

    private $end_process;

    public function __construct($interval)
    {
        $this->incoming     = new DateTime();
        $this->process_time = new DateInterval($interval);
    }

    public function startProcess()
    {
        $this->startProcess = new DateTime();
        $this->endProcess   = $this->startProcess->diff($this->process_time);
    }
}

