<?php


namespace App\controller;


abstract class AbstractController implements IController
{
    use TChainOfResponsibility;

    abstract function run();
}