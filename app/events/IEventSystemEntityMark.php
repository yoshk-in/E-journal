<?php


namespace App\events;


interface IEventSystemEntityMark
{
    public function getEventMark(): string;
}