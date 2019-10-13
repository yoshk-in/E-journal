<?php


namespace App\CLI\render;


use App\events\ISubscriber;


class InfoManager implements ISubscriber
{
    private $dispatchResolver;
    private $infoDispatchers = [];


    public function __construct(DispatchResolver $dispatchResolver)
    {
        $this->dispatchResolver = $dispatchResolver;
    }

    public function update(Object $observable, string $event)
    {
        $dispatcher = $this->dispatchResolver->getDispatcher($event);
        $dispatcher->handle($observable);
        $this->infoDispatchers[] = $dispatcher;
    }

    public function flush()
    {
        foreach ($this->infoDispatchers as $dispatcher) {
            $dispatcher->flush();
        }
    }



}