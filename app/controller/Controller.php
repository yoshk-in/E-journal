<?php


namespace App\controller;


use App\base\AbstractRequest;
use App\command\CmdResolver;
use App\CLI\render\InfoManager;
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

    public function run(AbstractRequest $request)
    {
        $this->request = $request;
        $commands = $this->commandResolver->getCommand($request);
        foreach ($commands as $command) {
            $output[] = $command->execute($request);
        }
        $this->infoDispatcher->flush($request);
    }

}