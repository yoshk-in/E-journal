<?php


namespace App\events;


trait TListenable
{
    use TObservable {
        notify as _notify;
    }

    private $listeners = [];

    public function attach(ICellSubscriber $subscriber)
    {
        $this->listeners[] = $subscriber;
    }

    public function notify(string $event)
    {
        $this->_notify($event);
        foreach ($this->listeners as $listener) {
            $listener->notify($this);
        }

    }


}