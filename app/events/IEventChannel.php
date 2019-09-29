<?php


namespace App\events;


interface IEventChannel
{
    public function notify($object, string $event);
}