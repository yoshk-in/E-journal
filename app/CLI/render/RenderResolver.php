<?php


namespace App\CLI\render;

use App\base\AppMsg;
use App\CLI\render\event\Info;
use App\CLI\render\event\Move;
use App\CLI\render\event\RangeInfo;
use Psr\Container\ContainerInterface;

class RenderResolver
{
    private $appContainer;
    private $eventMap = [
        AppMsg::ARRIVE      => Move::class,
        AppMsg::DISPATCH    => Move::class,
        AppMsg::INFO        => Info::class,
        AppMsg::RANGE_INFO  => RangeInfo::class
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