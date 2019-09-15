<?php

namespace App\command;

use App\base\AbstractRequest;
use App\base\exceptions\AppException;
use Psr\Container\ContainerInterface;

class CommandResolver
{
    private $baseCmd = 'Command';
    private $defaultCmd = FullInfoCommand::class;
    private $request;
    private $container;


    public function __construct(AbstractRequest $request, ContainerInterface $container)
    {
        $this->request = $request;
        $this->container = $container;
    }


    public function getCommand() : array
    {
        $result_cmd_array = [];
        $commands = $this->request->getCommands();
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
                    $result_cmd_array[] = $this->container->get($class);
                } else {
                    throw new AppException(
                        "The command class not found: $class is given"
                    );
                }
            }
            return $result_cmd_array;
        } else {
            return $this->container->get($this->defaultCmd);
        }
    }
}
