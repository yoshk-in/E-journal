<?php


namespace App\domain;


interface ISubscriber
{
    public function notify(Informer $observable);
}