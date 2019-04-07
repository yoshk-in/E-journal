<?php

namespace App\console;

use App\base\Request;

class G9Parser extends ConsoleSyntaxParser
{
    protected static function doParse(Request $request)
    {
        if (isset($_SERVER['argv'][2]))
        {
            self::setCommand($request);
        }

    }

    protected static function setCommand(Request $request)
    {
        $arg2 = $_SERVER['argv'][2];
        if ($arg2 === '+') {
            $request->setCommand('addObject');
        };
        if (mb_stripos($arg2, 'партия=') !== false) {
            list($key, $value) = explode('=', $arg2);
            $request->setCommand('setPartNumber');
            $request->setPartNumber($value);
        }

    }


}
