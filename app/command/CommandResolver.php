<?php

namespace App\command;

use App\base\Request;
use App\base\exceptions\AppException;

class CommandResolver
{
    private static $_baseCmd    = 'Command';
    private static $_defaultCmd = 'App\command\DefaultCommand';
    private static $_inst;



    private function __construct()
    {
    }

    public static function init()
    {
        if (is_null(self::$_inst)) {
            return new self;
        }
        return self::$_inst;

    }

    public static function getCommand(Request $request)
    {
        $result_cmd_array = [];
        $commands = $request->getCommands();
        if ($commands) {
            foreach ($commands as $command) {
                $command = ucfirst($command) . self::$_baseCmd;
                $file_of_command = __DIR__ . '/' . $command . '.php';
                $class = '\\' . __NAMESPACE__ . '\\' . $command;

                if (file_exists($file_of_command)) {
                    require_once $file_of_command;
                } else {
                    throw new AppException(
                        "The command file not found: $file_of_command is given"
                    );
                }

                if (class_exists($class)) {
                    $result_cmd_array[] = new $class;
                } else {
                    throw new AppException(
                        "The command class not found: $class is given"
                    );
                }
            }
            return $result_cmd_array;
        } else {

            return new self::$_defaultCmd;
        }
    }
}
