<?php


namespace App\GUI\services;


use App\events\IEvent;
use App\events\EventChannel;
use App\events\IObservable;
use App\events\TObservable;

class AlertMessage implements IObservable
{
    use TObservable;

    private string $message;

    public function __construct(string $message, EventChannel $eventChannel)
    {
        $this->message = $message;
        self::attachToStaticProperty($eventChannel);
    }

    public function send()
    {
        $this->update(IEvent::ALERT);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

}