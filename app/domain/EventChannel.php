<?php


namespace App\domain;

class EventChannel implements IEventChannel
{
    protected $subscribers;

    public function __construct(array $subscribers, array $observables)
    {
        foreach ($subscribers as $subscriber) {
            if (!$subscriber instanceof ISubscriber) throw new \Exception('subscriber must implements ISubscriber interface');
        }
        $this->subscribers = $subscribers;
        foreach ($observables as $observable) {
            if (array_search(IObservable::class, class_implements($observable)) === false) throw new \Exception('observable must implements IObservable interface');
            $observable::attachToEventChannel($this);
        }
    }

    public function notify($object)
    {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->notify($object);
        }
    }

}