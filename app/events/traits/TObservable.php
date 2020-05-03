<?php


namespace App\events\traits;


use App\events\Event;
use App\events\HierarchyEvent;
use App\events\IEventType;
use App\events\IObservable;

trait TObservable
{
    protected static string $eventSystemClassMark = __CLASS__;

    public static function getClassMark(): string
    {
        return self::$eventSystemClassMark;
    }

    public function getParentClassMark(): string
    {
        return parent::$eventSystemClassMark;
    }

    public function event($event = IEventType::ANY)
    {
        Event::create($this, $event);
    }

    public function bubbleUpEvent($event = IEventType::ANY)
    {
        parent::event($event);
        Event::create($this, $event);
    }

}