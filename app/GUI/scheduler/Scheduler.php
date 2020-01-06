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


    public function addTask(\DateInterval $time,\Closure $closure)
    {
        $this->loop->addTimer($this->roundTime($time), fn () => $closure()
        );
    }

    public function alert(string $msg)
    {
        $this->notify(Event::ALERT, $msg);
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