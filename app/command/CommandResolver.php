<?php

namespace App\command;

use App\base\AbstractRequest;
use App\base\exceptions\AppException;

class CommandResolver
{
    private $baseCmd = 'Command';
    private $defaultCmd = 'App\command\DefaultCommand';


    public function getCommand(AbstractRequest $request)
    {
        $result_cmd_array = [];
        $commands = $request->getCommands();
        if ($commands) {
            foreach ($commands as $command) {
                $command = ucfirst($command) . $this->baseCmd;
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
            return new $this->defaultCmd;
        }
    }
}
