<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class RangeInfo extends Parser
{

    protected function doParse($request)
    {
        $request->addCmd(AppMsg::RANGE_INFO);
    }


}