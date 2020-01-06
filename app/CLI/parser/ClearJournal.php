<?php


namespace App\CLI\parser;


use App\base\AppMsg;

class ClearJournal extends Parser
{

    protected function doParse()
    {
        $this->request->addCmd(AppMsg::CLEAR_JOURNAL);
    }
}