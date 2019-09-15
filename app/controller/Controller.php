<?php

namespace App\controller;


use App\command\CommandResolver;
use App\console\ConsoleParser;
use App\console\Render;
use App\domain\EventChannel;

class Controller
{
    private $consoleParser;
    private $render;
    private $commandResolver;
    private $eventChannel;

    public function __construct(
        ConsoleParser $consoleParser,
        CommandResolver $commandResolver,
        Render $render,
        EventChannel $eventChannel
    ) {
        $this->consoleParser = $consoleParser;
        $this->commandResolver = $commandResolver;
        $this->render = $render;
        $this->eventChannel = $eventChannel;
    }

    public function handleConsoleRequest()
    {
        $this->consoleParser->parseAndFillRequestWithCommands();
        $commands = $this->commandResolver->getCommand();
        foreach ($commands as $command) {
            $output[] = $command->execute();
        }
        $this->render->flush();
    }

}
