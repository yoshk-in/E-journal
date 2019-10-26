<?php


namespace App\controller;

;
use App\infoManager\InfoManager;
use App\command\CmdResolver;
use App\events\EventChannel;

class Controller
{
    protected $infoMng;
    protected $commandResolver;
    protected $request;

    //needs only to tracking products and procedures by render
    private $eventChannel;

    public function __construct(
        CmdResolver $commandResolver,
        InfoManager $infoMng,
        EventChannel $eventChannel
    ) {
        $this->commandResolver = $commandResolver;
        $this->infoMng = $infoMng;
        $this->eventChannel = $eventChannel;
    }

    public function run()
    {
        $commands = $this->commandResolver->getCommand();
        foreach ($commands as $command) {
            $output[] = $command->execute();
        }
        $this->infoMng->dispatch();
    }

}