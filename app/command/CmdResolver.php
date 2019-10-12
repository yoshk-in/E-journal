<?php

namespace App\command;

use App\base\AbstractRequest;
use App\base\exceptions\AppException;
use Psr\Container\ContainerInterface;

class CmdResolver
{
    private $baseCmd = '';
    private $defaultCmd = Info::class;
    private $request;
    private $container;



    public function __construct(ContainerInterface $container)
    {

        $this->container = $container;
    }


    public function getCommand(AbstractRequest $request) : array
    {
        $this->request = $request;
        $result_cmd_array = [];
        $commands = $this->request->getCmd();
        if (!empty($commands)) {
            foreach ($commands as $command) {
                $command = $command . $this->baseCmd;
                $file_of_command = __DIR__ . DIRECTORY_SEPARATOR . $command . '.php';
                $class = '\\' . __NAMESPACE__ . '\\' . $command;

                if (!file_exists($file_of_command)) {
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
