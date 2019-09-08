<?php


namespace App\domain;


trait TObservable
{
    static private $eventChannel;

    static function addEventChannel($channel)
    {
        self::$eventChannel = $channel;
    }
}