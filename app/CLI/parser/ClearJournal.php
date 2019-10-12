<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class ClearJournal extends Parser
{

    protected function doParse($request)
    {
        $request->addCmd(AppMsg::CLEAR_JOURNAL);
    }
}