<?php


namespace App\events;


interface ISubscriber
{
    public function update(Object $observable, string $event);
}