<?php

namespace App\command;

use \App\base\Request;

abstract class Command
{
    final public function __construct()
    {

    }

    public function execute(Request $request)
    {
        $this->doExecute($request);
    }

    abstract protected function doExecute(Request $request);
}
