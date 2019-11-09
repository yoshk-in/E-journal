<?php


namespace App\GUI;


class MouseMng
{
    private $currentStrategy;

    public function __construct(NewClickHandler $currentStrategy)
    {
        $this->currentStrategy = $currentStrategy;
    }


    public function changeHandler(ClickHandler $strategy): void
    {
       $this->currentStrategy = $strategy;
    }

    public function getHandler()
    {
        return $this->currentStrategy;
    }
}