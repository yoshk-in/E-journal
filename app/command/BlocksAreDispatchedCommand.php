<?php


namespace App\command;


use App\base\Request;

class BlocksAreDispatchedCommand extends Command
{
    protected function doExecute(Request $request)
    {
        echo __CLASS__;
    }
}