<?php


namespace App\events;


trait TCasualSubscriber
{
    public function subscribeOn(): array
    {
        return self::EVENTS;
    }
}