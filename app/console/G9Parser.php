<?php

namespace App\console;

use App\base\Request;

class G9Parser extends ConsoleSyntaxParser
{
    protected static function doParse(Request $request)
    {
        switch ($_SERVER['argv'][2]) {
            case '+':
                $request->setProperty('cmd', 'addObject');
                break;
            case 'партия':

            default:
                # code...
                break;
        }

    }
}
