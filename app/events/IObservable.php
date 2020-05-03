<?php


namespace App\events;

interface IObservable
{
    static function attachToStaticProperty(IEventChannel $channel);

    public function update(Event $event);


}