<?php

namespace App\command;

use \App\base\Request;
use \App\base\AppHelper;


class SetPartNumberCommand extends Command
{
    protected function doExecute(Request $request)
    {
        $partNumber = $request->getPartNumber();
        $cache = AppHelper::getCacheObject();
        $cache->set('partNumber', $partNumber);
        $request->setFeedback("Г9");
        $request->setFeedback('установлен номер партии ' . $partNumber);
    }
}
