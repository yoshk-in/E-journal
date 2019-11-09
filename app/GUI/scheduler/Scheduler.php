<?php


namespace App\GUI\scheduler;


use React\EventLoop\LoopInterface;

class Scheduler
{

    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function addTask(\DateInterval $time, \Closure $callback)
    {
        $this->loop->addTimer((int) 1 + $time->s, $callback);
    }
}