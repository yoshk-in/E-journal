<?php

namespace App\domain;



class Procedure extends State
{
    private $processTime;
    private $startProcess;
    private $endProcess;

    public function __construct($name, DomainObject $product, $idState, $interval)
    {
        parent::__construct($name, $product, $idState);
        $this->processTime = new \DateInterval($interval);
        $this->startProcess = new \DateTime('now');
        $this->endProcess = clone $this->startProcess;
        $this->endProcess->add($this->processTime);
    }

    /**
     * @return mixed
     */
    public function getProcessTime()
    {
        return $this->processTime;
    }

    /**
     * @param mixed $process_time
     *P    public function setProcessTime($processTime): void
    {
        $this->processTime = $processTime;
    }

    /**
     * @return mixed
     */
    public function getStartProcess()
    {
        return $this->startProcess;
    }

    /**
     * @param mixed $start_process
     */
    public function setStartProcess($startProcess): void
    {
        $this->startProcess = $startProcess;
    }

    /**
     * @return mixed
     */
    public function getEndProcess()
    {
        return $this->endProcess;
    }

    /**
     * @param mixed $end_process
     */
    public function setEndProcess($endProcess): void
    {
        $this->endProcess = $endProcess;
    }

}

