<?php

namespace App\command;

use App\base\Request;
use App\domain\G9;

class AddObjectCommand extends Command
{
    protected function doExecute(Request $request)
    {
        $blocks = $request->getBlockNumbers();
        foreach ($blocks as $block) {
            $object = new G9($block);
        }
        var_dump($block);
    }
}
