<?php


namespace App\GUI;


class MouseHandlerMng
{
    private ?ClickHandler $currentStrategy;

    public function __construct()
    {
        $this->currentStrategy = null;
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