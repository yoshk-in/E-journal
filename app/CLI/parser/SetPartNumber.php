<?php


namespace App\CLI\parser;


use App\base\AppCmd;

class SetPartNumber extends Parser
{

    protected function doParse()
    {
        $this->request->addCmd(AppCmd::SET_PART_NUMBER);
    }
}