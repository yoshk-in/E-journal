<?php


namespace App\GUI;


class MouseHandlerMng
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

    public function getHandler() : ClickHandler
    {
        return $this->currentStrategy;
    }
}