<?php

namespace App\command;

class AddPartNumberCommand extends Command
{
    public function doExecute($request)
    {
        $partNumber = $request->getProperty('partNumber');
    }
}
