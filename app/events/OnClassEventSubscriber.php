<?php


namespace App\events;


use App\events\traits\TObservable;
use App\infoManager\CLIInfoManager;

class OnClassEventSubscriber
{
    public static function create($subscriber, array $onEvents): \stdClass
    {
        $on = new \stdClass();
        $on->observableClass =  $class = array_key_first($onEvents);
        $on->event = $onEvents[$class][1] ?? IEventType::ANY;
        $on->closure = \Closure::fromCallable([$subscriber, $onEvents[$class][0]]);
        return $on;
    }

}