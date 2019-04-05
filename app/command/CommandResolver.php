<?php

namespace App\command;

use App\base\Request;

class CommandResolver
{
    private static $base_cmd    = 'Command';
    private static $default_cmd = 'App\command\DefaultCommand';

    public static function getCommand(Request $request)
    {
        if ($cmd = $request->getProperty('cmd')) {
            $cmd   = ucfirst($cmd) . self::$base_cmd;
            $file  = __DIR__ . '/' . $cmd . '.php';
            $class = '\\' . __NAMESPACE__ . '\\' . $cmd;

            if (file_exists($file)) {
                require_once $file;
            } else {
                throw new \App\base\AppException("The command file not found: $file is given");}

            if (class_exists($class)) {
                return new $class;
            } else {throw new \App\base\AppException("The command class not found: $class is given");}

        } else {
            return new self::$default_cmd;
        }
    }
}
