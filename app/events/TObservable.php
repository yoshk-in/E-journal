<?php


namespace App\events;


trait TObservable
{
    static private $eventChannel;

    static function attachToEventChannel(IEventChannel $channel)
    {
        self::$eventChannel = $channel;
    }

    public function notify(string $event, $msg = null)
    {
        self::$eventChannel->notify($msg ?? $this, $event);
    }



}