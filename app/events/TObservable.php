<?php


namespace App\events;


trait TObservable
{
    static private $eventChannel;

    static function attachToEventChannel(IEventChannel $channel)
    {
        self::$eventChannel = $channel;
    }

    public function notify(string $event)
    {
        self::$eventChannel->notify($this, $event);
        if (isset($this->listeners)) {
            foreach ($this->listeners as $listener) {
                $listener->notify($this);
            }
        }
    }



}