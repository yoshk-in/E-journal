<?php


namespace App\events;

class EventChannel implements IEventChannel
{

    protected $channels = [];

    public function __construct(array $subscribers, array $observables)
    {

        foreach ($subscribers as $subscriber) {
            if (!$subscriber instanceof ISubscriber) throw new \Exception('subscriber must implements ISubscriber interface');
            foreach ($subscriber->subscribeOn() as $event) {
                $this->channels[$event][] = $subscriber;
            };
        }

        foreach ($observables as $observable) {
            if (array_search(IObservable::class, class_implements($observable)) === false) throw new \Exception('observable must implements IObservable interface');
            $observable::attachToEventChannel($this);
        }
    }

    public function notify($object, string $event)
    {
//        if (!array_key_exists($event, $this->channels)) return;
        foreach ($this->channels[$event] as $subscriber) {
            $subscriber->update($object, $event);
        }
    }

    public function subscribe(ISubscriber $subscriber)
    {
        foreach ($subscriber->subscribeOn() as $event) {
            $this->channels[$event][] = $subscriber;
        };
    }

}