<?php


namespace App\events;

class EventChannel implements IEventChannel
{

    protected $channels = [];

    public function __construct(array $subscribers, array $observables)
    {
        $this->subscribeArray($subscribers);
        $this->attachStaticArray($observables);
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
            $this->channels[$event][$this->subscriberKey($subscriber)] = $subscriber;
        };
    }

    public function describe(ISubscriber $subscriber, string $event)
    {
        $key = $this->subscriberKey($subscriber);
        if (isset($this->channels[$event][$key])) {
            unset($this->channels[$event][$key]);
        }
    }

    public function subscribeArray(array $subscribers)
    {
        foreach ($subscribers as $subscriber) {
            $this->subscribe($subscriber);
        }
    }

    public function attachStaticArray(array $observables)
    {
        foreach ($observables as $observable) {
            $this->attachStatic($observable);
        }
    }

    public function attachStatic($observable)
    {
        $observable::attachToEventChannel($this);
    }

    private function subscriberKey($subscriber): string
    {
        return get_class($subscriber);
    }

}