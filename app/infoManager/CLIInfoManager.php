<?php


namespace App\infoManager;


use App\base\AppMsg;
use App\events\Event;
use App\events\EventChannel;
use App\events\ISubscriber;


class CLIInfoManager implements ISubscriber
{
    private DispatchResolver $dispatchResolver;
    private array $events = [];

    const SUBSCRIBE_ON = [
        Event::PROCEDURE_CHANGE_STATE,
        AppMsg::PRODUCT_INFO,
        AppMsg::RANGE_INFO,
        AppMsg::NOT_FOUND,
    ];


    public function __construct(DispatchResolver $dispatchResolver, EventChannel $channel)
    {
        $this->dispatchResolver = $dispatchResolver;
        $channel->subscribe($this);
    }

    public function update($observable, string $event)
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