<?php


namespace App\events;


interface IListenable
{
    public function attach(ICellSubscriber $subscriber);

    public function notify(string $event);
}