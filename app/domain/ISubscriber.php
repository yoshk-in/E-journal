<?php


namespace App\domain;


interface ISubscriber
{
    public function update(Informer $observable);
}