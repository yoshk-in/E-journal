<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class Info extends Parser
{

    protected function doParse($request)
    {
        $request->addCmd(AppMsg::PRODUCT_INFO);
    }
}