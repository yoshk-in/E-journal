<?php


namespace App\controller;

;
use App\CLI\render\InfoManager;
use App\command\CmdResolver;
use App\events\EventChannel;

class Controller
{
    protected $infoDispatcher;
    protected $commandResolver;
    protected $request;

    //needs only to init for tracking products and procedures by render
    private $eventChannel;

    public function __construct(
        CmdResolver $commandResolver,
        InfoManager $infoDispatcher,
        EventChannel $eventChannel
    ) {
        $this->commandResolver = $commandResolver;
        $this->infoDispatcher = $infoDispatcher;
        $this->eventChannel = $eventChannel;
    }

    public function run()
    {
        $commands = $this->commandResolver->getCommand();
        foreach ($commands as $command) {
            $output[] = $command->execute();
        }
        $this->infoDispatcher->flush();
    }

}