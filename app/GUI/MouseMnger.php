<?php


namespace App\GUI;


class MouseMnger
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