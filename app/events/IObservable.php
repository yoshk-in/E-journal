<?php


namespace App\events;

interface IObservable
{
    static function attachToEventChannel(IEventChannel $channel);

    public function notify(string $event);
}