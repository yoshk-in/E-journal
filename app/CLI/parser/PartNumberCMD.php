<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class PartNumberCMD extends Parser
{

    protected function doParse()
    {
        $this->request->addCmd(AppMsg::PARTY);
    }
}