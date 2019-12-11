<?php


namespace App\events;


interface ISubscriber
{
    public function update($observable, string $event);

    public function subscribeOn(): array;
}