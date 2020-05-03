<?php


namespace App\events;

use App\events\traits\TObservable;

/**
 * Class Event
 * @package App\events
 * @property string $class;
 * @property $observable;
 * @property string $type
 */
class Event implements IEventType
{
    protected string $class;
    protected $observable;
    protected string $type;

    protected static EventChannel $eventChannel;

    public function __construct($observable, string $eventType)
    {
        $this->class = $observable::getClassMark();
        $this->observable = $observable;
        $this->type = $eventType;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __toString()
    {
        return EventChannel::subscriberKey($this->class) . $this->type;
    }


    public static function create($entity, $eventType = IEventType::ANY)
    {
        (self::$eventChannel)->update(new static($entity, (string) $eventType));
    }

    public static function startEvent($entity)
    {
        static::create($entity, IEventType::START);
    }

    public static function endEvent($entity)
    {
        static::create($entity, IEventType::END);
    }

    public static function report($entity)
    {
        static::create($entity, IEventType::REPORT);
    }

    public static function toDoEvent($entity)
    {
        // @TODO: create changeNumberEvent to Product
        exit('todo event changeNumberProductEvent');

    }

    public static function connectToEventChannel(EventChannel $eventChannel)
    {
        self::$eventChannel = $eventChannel;
    }


}