<?php


namespace App\GUI\startMode;


use App\base\AppMsg;
use App\events\EventChannel;
use App\events\ISubscriber;
use Psr\Container\ContainerInterface;

class ModeManager implements ISubscriber
{
    protected $app;
    const EVENTS = [
        AppMsg::NOT_FOUND,
        AppMsg::GUI_INFO
    ];
    const MODES = [
        AppMsg::NOT_FOUND => FirstStart::class,
        AppMsg::GUI_INFO => MainMode::class
    ];

    private $container;
    private $channel;

    public function __construct(ContainerInterface $container, EventChannel $channel)
    {
        $this->container = $container;
        $this->channel = $channel;
    }


    public function update(Object $observable, string $event)
    {
        $mode = $this->container->get(self::MODES[$event]);
        if ($event === AppMsg::GUI_INFO) $this->channel->describe($this, AppMsg::GUI_INFO);
        $mode->run();
    }

    public function subscribeOn(): array
    {
        return self::EVENTS;
    }

}