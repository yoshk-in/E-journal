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
    const ALERT = true;

    public function __construct(LoopInterface $loop, EventChannel $channel)
    {
        $this->loop = $loop;
        $this->channel = $channel;
        $this->channel->attach($this);
    }


    public function addTask(\DateInterval $time, array $callbacks)
    {
        $this->addTimer($time, \Closure::fromCallable(call_user_func([$this, 'doTasks'], $callbacks)));
    }

    public function addTaskWithAlert(\DateInterval $time, array $callbacks, array $alerts)
    {
        $this->addTimer($time, \Closure::fromCallable(call_user_func([$this, 'tasksWithAlerts'], $callbacks, $alerts)));
    }

    public function asyncFutureTick(\Closure $closure)
    {
        $this->loop->futureTick($closure);
    }

    private function tasksWithAlerts($tasks, $alerts)
    {
        $this->doTasks($tasks);
        $this->doTasks($alerts, self::ALERT);
    }

    private function addTimer($time, \Closure $closure)
    {
        $this->loop->addTimer($this->roundTime($time), $closure);
    }

    private function doTasks($tasks, bool $isAlert = false)
    {
        foreach ($tasks as $task) {
            assert(is_callable($task), ' task must be callable');
            $msg = $task();
            $isAlert ? $this->notify(Event::ALERT, $msg) : null;
        }
    }

    private function roundTime($time): int
    {
        return (int) 1 + $time->s;
    }
}