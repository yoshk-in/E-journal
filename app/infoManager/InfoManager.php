<?php


namespace App\infoManager;


use App\base\AppMsg;
use App\events\ISubscriber;


class InfoManager implements ISubscriber
{
    private $dispatchResolver;
    private $events = [];

    const SUBSCRIBE_ON = [
        AppMsg::DISPATCH,
        AppMsg::ARRIVE,
        AppMsg::INFO,
        AppMsg::RANGE_INFO,
        AppMsg::NOT_FOUND,
    ];


    public function __construct(DispatchResolver $dispatchResolver)
    {
        $this->dispatchResolver = $dispatchResolver;
    }

    public function update(Object $observable, string $event)
    {
        $dispatcher = $this->dispatchResolver->getDispatcher($event);
        $dispatcher->handle($observable);
        $this->events[$event] = $dispatcher;
    }

    public function dispatch()
    {
        foreach ($this->events as $dispatcher) {
            $dispatcher->flush();
        }
    }


    public function subscribeOn(): array
    {
        return self::SUBSCRIBE_ON;
    }
}