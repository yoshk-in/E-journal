<?php


namespace App\GUI\scheduler;


use App\events\Event;
use App\events\EventChannel;
use App\events\IObservable;
use App\events\TObservable;
use React\EventLoop\LoopInterface;

class Scheduler implements IObservable
{
    use TObservable;

    private $loop;
    private $alert;
    private $channel;

    public function __construct(LoopInterface $loop, EventChannel $channel)
    {
        $this->loop = $loop;
        $this->channel = $channel;
        $this->channel->attach($this);
    }


    public function addTask(\DateInterval $time,\Closure $closure, ?string $alert = null)
    {
        $this->loop->addTimer($this->roundTime($time), function () use ($closure, $alert) {
            $closure();
            !$alert ?: $this->notify(Event::ALERT, $alert);
        });
    }


    public function asyncFutureTick(\Closure $closure)
    {
        $this->loop->futureTick($closure);
    }


    private function roundTime($time): int
    {
        return (int) 1 + $time->s;
    }
}