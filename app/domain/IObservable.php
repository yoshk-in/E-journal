<?php


namespace App\domain;

interface IObservable
{
    static function attachToEventChannel(IEventChannel $channel);

    public function notify();
}