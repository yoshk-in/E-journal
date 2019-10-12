<?php


namespace App\controller;


trait TChainOfResponsibility
{
    private $next;

    public function setNextHandler($handler)
    {
        $this->next = $handler;
        return $this;
    }
}