<?php


namespace App\GUI\handlers;


use App\events\Event;
use App\events\EventChannel;
use App\events\ISubscriber;
use Gui\Application;

class Alert implements ISubscriber, Event
{
    private $gui;
    const EVENTS = [
        Event::ALERT
    ];

    public function __construct(Application $gui, EventChannel $channel)
    {
        $this->gui = $gui;
        $channel->subscribe($this);
    }
    public function alert(string $msg)
    {
        $this->gui->alert($msg);
    }

    public function __invoke(string $msg)
    {
        $this->alert($msg);
    }

    public function update($msg, string $event)
    {
        $this->alert($msg);
    }

    public function subscribeOn(): array
    {
        return self::EVENTS;
    }
}