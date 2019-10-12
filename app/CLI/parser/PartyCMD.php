<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class PartyCMD extends Parser
{

    protected function doParse($request)
    {
        $request->addCmd(AppMsg::PARTY);
    }
}