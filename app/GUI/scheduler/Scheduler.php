<?php


namespace App\GUI\scheduler;


use App\events\IEvent;
use App\events\EventChannel;
use App\events\IObservable;
use App\events\TObservable;
use Closure;
use DateInterval;
use React\EventLoop\LoopInterface;

class Scheduler
{

    private LoopInterface $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }


    public function addTask(DateInterval $time, Closure $closure)
    {
        $this->loop->addTimer($this->roundTime($time), fn () => $closure()
        );
    }


    public function asyncFutureTick(Closure $closure)
    {
        $this->loop->futureTick($closure);
    }


    private function roundTime($time): int
    {
        return (int) 1 + $time->s;
    }
}