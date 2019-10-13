<?php


namespace App\controller;


interface IController
{
    public function run();
    
    public function setNextHandler($controller);
}