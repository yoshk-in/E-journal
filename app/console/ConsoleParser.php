<?php

namespace App\console;

use \App\base\AppHelper;

abstract class ConsoleParser
{
    protected $request;

    public function __construct()
    {
        $this->request = AppHelper::getRequest();

    }


    public function parse()
    {
        $params = $_SERVER['argv'];
        $args_counter      = 0;
        foreach ($params as $argument) {
            $this->request->setProperty(++$args_counter, $argument);
        }
        $this->doParse();
    }

    abstract protected function doParse();
}
