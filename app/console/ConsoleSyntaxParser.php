<?php

namespace App\console;

use App\base\Request;

abstract class ConsoleSyntaxParser
{

    public static function parse(Request $request)
    {
        $params = $_SERVER['argv'];
        $i      = 0;
        foreach ($params as $arg) {
            $request->setProperty($i++, $arg);
        }
        static::doParse($request);

    }

    abstract protected static function doParse(Request $request);
}
