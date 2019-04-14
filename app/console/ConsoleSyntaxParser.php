<?php

namespace App\console;

use \App\base\AppHelper;

abstract class ConsoleSyntaxParser
{
    protected $request;

    public function __construct()
    {
        $this->request = AppHelper::getRequest();

    }


    public function parse()
    {
        $params = $_SERVER['argv'];
        $i      = 0;
        foreach ($params as $arg) {
            $this->request->setProperty($i++, $arg);
        }
        $this->doParse();
    }

    abstract protected function doParse();
}
