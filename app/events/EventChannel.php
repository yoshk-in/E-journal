<?php


namespace App\events;

class EventChannel implements IEventChannel
{

    protected $channels = [];

    public function __construct(array $subscribers, array $observables)
    {
        $this->subscribeArray($subscribers);
        $this->attachArray($observables);
    }

    public function notify($object, string $event)
    {
        if (!key_exists($event, $this->channels)) return;
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

    public function unsubscribe(ISubscriber $subscriber, string $event)
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

    public function attachArray(array $observables)
    {
        foreach ($observables as $observable) {
            $this->attach($observable);
        }
    }

    public function attach($observable)
    {
        $observable::attachToEventChannel($this);
    }

    private function subscriberKey($subscriber): string
    {
        return get_class($subscriber);
    }

}