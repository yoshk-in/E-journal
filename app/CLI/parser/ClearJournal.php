<?php


namespace App\CLI\parser;


use App\base\AppCmd;

class ClearJournal extends Parser
{

    protected function doParse()
    {
        $this->request->addCmd(AppCmd::CLEAR_JOURNAL);
    }
}