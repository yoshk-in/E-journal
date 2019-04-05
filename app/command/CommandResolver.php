<?php

namespace app\command;

class CommandResolver
{
    public static function getCommand(\app\base\Request $request)
    {
        if ($request->getProperty('cmd')) {
            return echo $request->getProperty('cmd');
        } else {
            return echo 'default command';
        }
    }

}
