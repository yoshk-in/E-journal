<?php


namespace App\controller;


trait TChainOfResponsibility
{
    protected self $next;

    public function setNext($handler)
    {
        $this->next = $handler;
        return $this;
    }
}