<?php


namespace App\domain;


use App\events\Event;
use App\events\traits\TObservable;
use Doctrine\ORM\Mapping\Entity;

/** @Entity() */
class ConcreteProduct extends AbstractProduct
{
    use TObservable{TObservable::event as childEvent;}

    protected string $name;

    public function event($event = Event::ANY)
    {
        parent::event($event);
        $this->childEvent($event);
    }
}