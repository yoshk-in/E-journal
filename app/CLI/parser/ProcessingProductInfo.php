<?php


namespace App\CLI\parser;


use App\base\AppCmd;

class ProcessingProductInfo extends Parser
{

    protected function doParse()
    {
        $this->request->addCmd(AppCmd::FIND_UNFINISHED);
    }
}