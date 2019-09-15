<?php


namespace App\domain;


interface IEventChannel
{
    public function notify($object);
}