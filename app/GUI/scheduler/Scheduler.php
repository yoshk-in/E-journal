<?php


namespace App\GUI\scheduler;


use App\GUI\GUIManager;
use React\EventLoop\LoopInterface;

class Scheduler
{

    private $loop;
    private $app;

    public function __construct(LoopInterface $loop, GUIManager $manager)
    {
        $this->loop = $loop;
        $this->app = $manager;
    }

    public function addTask(\DateInterval $time, \Closure $callback)
    {
        $this->loop->addTimer((int) 1 + $time->s, function () use ( $callback) {
            $alertMsg = $callback();
            is_null($alertMsg) ?: $this->app->alert($alertMsg);
        });
    }
}