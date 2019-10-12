<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class Arrive extends Parser
{

    protected function doParse($request)
    {
        $request->addCmd(AppMsg::ARRIVE);
    }
}