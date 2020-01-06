<?php


namespace App\controller;


use App\base\AbstractRequest;
use App\command\CmdResolver;
use App\domain\Product;
use App\domain\ProductMap;
use App\events\EventChannel;

class Controller
{
    protected CmdResolver $commandResolver;
    protected AbstractRequest $request;
    private ProductMap $productMap;

    //needs to tracking products and procedures by info dispatching and for persisting to database
    private EventChannel $eventChannel;


    public function __construct(
        CmdResolver $commandResolver,
        EventChannel $eventChannel,
        ProductMap $productMap
    ) {
        $this->commandResolver = $commandResolver;
        $this->eventChannel = $eventChannel;
        $this->productMap = $productMap;
    }

    public function run()
    {
        Product::setNumberStrategy($this->productMap->getNumberStrategy($this->request->getProduct()));
        $commands = $this->commandResolver->getCommand();
        foreach ($commands as $command) {
            $command->execute();
        }
    }

}