<?php


namespace App\domain;


trait TObservable
{
    static private $eventChannel;

    static function attachToEventChannel(IEventChannel $channel)
    {
        if (!empty($channel)) {
            self::$eventChannel = $channel;
            return;
        }
        throw new \Exception('event channel is empty');
    }

    public function notify()
    {
        self::$eventChannel->notify($this);
    }

}