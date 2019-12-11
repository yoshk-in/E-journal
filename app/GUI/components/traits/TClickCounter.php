<?php


namespace App\GUI\components\traits;


trait TClickCounter
{
    private $clickCounter = 0;
    private $defaultBorderColor;

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