<?php

namespace App\command;

use App\base\Request;

class AddObjectCommand extends Command
{
    protected function doExecute(Request $request)
    {
        echo __CLASS__;

    }
}
