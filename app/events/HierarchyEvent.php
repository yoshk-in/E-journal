<?php


namespace App\events;


use App\events\Event;
use App\events\IEventType;
use App\events\traits\TObservable;

class HierarchyEvent extends Event
{

    protected function __construct($observable, string $eventType)
    {
        $this->class = $observable->getParentClassMark();
        $this->observable = $observable;
        $this->type = $eventType;
    }





}