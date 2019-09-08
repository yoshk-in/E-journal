<?php


namespace App\domain;


class EventChannel
{
    protected $subscribers;

    public function __construct(array $subscribers, array $observables)
    {
        foreach ($subscribers as $observable) {
            if (!$observable instanceof ISubscriber) throw new \Exception();
        }
        foreach ($observables as $observable) {

            if (array_search(IObservable::class, class_implements($observable)) === false) throw new \Exception();
            $observable::addEventChannel($this);
        }
    }

    public function notify($object)
    {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->notify($object);
        }
    }

}