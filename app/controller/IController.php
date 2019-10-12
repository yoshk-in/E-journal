<?php


namespace App\controller;


use App\base\AbstractRequest;

interface IController
{
    public function run();
    
    public function setNextHandler($controller);
}