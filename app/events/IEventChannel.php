<?php


namespace App\events;


interface IEventChannel
{
    public function update(Event $event);

    function subscribe(\stdClass $onEventProps);


}