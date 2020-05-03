<?php


namespace App\GUI\handlers;


use App\events\Event;
use App\events\IGUIEvent;
use Gui\Application;

class Alert implements IGUIEvent
{
    private Application $gui;
    const ALERT = 'alert';

    public function __construct(Application $gui)
    {
        $this->gui = $gui;
    }

    public function alert(Event $event)
    {
        $this->gui->alert($event->observable);
    }

    public function __invoke(string $msg)
    {
        $this->gui->alert($msg);
    }



}