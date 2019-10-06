<?php


namespace App\console\render;

use App\command\FullInfoCommand;
use App\command\RangeInfoCommand;
use App\console\render\event\BlocksMoveRender;
use App\console\render\event\FullInfoRender;
use App\console\render\event\RangeInfoRender;
use Psr\Container\ContainerInterface;

class RenderResolver
{
    private $appContainer;
    private $eventMap = [
        'setStart' => BlocksMoveRender::class,
        'setEnd' => BlocksMoveRender::class,
        FullInfoCommand::class => FullInfoRender::class,
        RangeInfoCommand::class => RangeInfoRender::class
    ];



    public function __construct(ContainerInterface $appContainer)
    {
        $this->appContainer = $appContainer;
    }

    public function getEventRender(string $event)
    {
        return $this->appContainer->get($this->eventMap[$event]);
    }


}