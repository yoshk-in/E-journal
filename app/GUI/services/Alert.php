<?php


namespace App\GUI\services;


use App\events\EventChannel;
use App\events\IEventChannel;
use App\events\IObservable;
use App\events\TObservable;

class Alert implements IObservable
{
    use TObservable;

    private EventChannel $eventChannel;

    public function __construct(EventChannel $eventChannel)
    {
        $this->eventChannel = $eventChannel;
    }


    public function send(string $msg)
    {
        $alert = new AlertMessage($msg, $this->eventChannel);
        $alert->send();
    }

}