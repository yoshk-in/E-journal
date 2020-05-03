<?php


namespace App\events;


interface ISubscriber
{
    public function notify(Event $event);

    public function subscribeOn(): array;
}