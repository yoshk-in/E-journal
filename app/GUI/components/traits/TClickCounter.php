<?php


namespace App\GUI\components\traits;


trait TClickCounter
{
    private int $clickCounter = 0;

    public function getClickCounter()
    {
        return $this->clickCounter;
    }


    public function plusClickCounter(): void
    {
        ++$this->clickCounter;
    }

    public function resetClickCounter()
    {
        $this->default();
        $this->clickCounter = 0;
    }

    abstract function default();
}