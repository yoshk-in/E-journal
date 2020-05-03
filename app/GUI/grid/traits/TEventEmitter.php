<?php


namespace App\GUI\grid\traits;


use Closure;

trait TEventEmitter
{
    protected array $eventHandlers = [];

    public function on(string $event, Closure $callback)
    {
        if (key_exists($event, $this->eventHandlers)) {
            $this->eventHandlers[$event] = [];
        }
        $this->eventHandlers[$event] = [$callback];
    }

    public function fire(string $event): void
    {
        if (!key_exists($event, $this->eventHandlers)) return;
        foreach ($this->eventHandlers[$event] as $handler) {
            $handler();
        }
    }
}