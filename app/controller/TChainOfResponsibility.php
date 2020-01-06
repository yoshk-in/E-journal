<?php


namespace App\controller;


trait TChainOfResponsibility
{
    private self $next;

    public function setNextHandler($handler)
    {
        $this->next = $handler;
        return $this;
    }
}