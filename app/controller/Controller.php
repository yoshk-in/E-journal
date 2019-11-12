<?php


namespace App\controller;

;

use App\GUI\GUIManager;
use App\infoManager\CLIInfoManager;
use App\command\CmdResolver;
use App\events\EventChannel;

class Controller
{
    protected $commandResolver;
    protected $request;

    //needs only to tracking products and procedures by info dispatching
    private $eventChannel;

    public function __construct(
        CmdResolver $commandResolver,
        EventChannel $eventChannel
    ) {
        $this->commandResolver = $commandResolver;
        $this->eventChannel = $eventChannel;
    }

    public function run()
    {
        $commands = $this->commandResolver->getCommand();
        foreach ($commands as $command) {
            $command->execute();
        }
    }

}