<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class Dispatch extends Parser
{

    protected function doParse($request)
    {
        $request->addCmd(AppMsg::DISPATCH);
    }
}