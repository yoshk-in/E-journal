<?php

namespace App\command;

use App\base\Request;

class CommandResolver
{
    private static $base_cmd    = 'Command';
    private static $default_cmd = 'App\command\DefaultCommand';
    private static $inst;



    private function __construct()
    {
    }

    public static function init()
    {
        if (is_null(self::$inst)) {
            return new self;
        }
        return self::$inst;

    }

    public static function getCommand(Request $request)
    {
        $cmdArray = [];
        if ($cmds = $request->getCommands()) {
            foreach ($cmds as $cmd) {
                $cmd = ucfirst($cmd) . self::$base_cmd;
                $file = __DIR__ . '/' . $cmd . '.php';
                $class = '\\' . __NAMESPACE__ . '\\' . $cmd;

                if (file_exists($file)) {
                    require_once $file;
                } else {
                    throw new \App\base\AppException("The command file not found: $file is given");
                }

                if (class_exists($class)) {
                    $cmdArray[] = new $class;
                } else {
                    throw new \App\base\AppException("The command class not found: $class is given");
                }
            }
            return $cmdArray;
        } else {

            return new self::$default_cmd;
        }
    }
}
