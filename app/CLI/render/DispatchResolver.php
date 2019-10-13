<?php


namespace App\CLI\render;

use App\base\AbstractRequest;
use App\base\AppMsg;
use App\CLI\render\event\Info;
use App\CLI\render\event\Move;
use App\CLI\render\event\RangeInfo;
use App\GUI\ResponseDispatcher;
use Psr\Container\ContainerInterface;


class DispatchResolver
{
    private $appContainer;
    private $eventMap = [];
    static private $CLI_MAP = [
        AppMsg::ARRIVE      => Move::class,
        AppMsg::DISPATCH    => Move::class,
        AppMsg::INFO        => Info::class,
        AppMsg::RANGE_INFO  => RangeInfo::class
    ];
    static private $GUI_MAP = [
        AppMsg::INFO        => ResponseDispatcher::class
    ];


    public function __construct(ContainerInterface $appContainer, AbstractRequest $request)
    {
        $this->appContainer = $appContainer;
        $this->eventMap = self::${$request->getEnv() . '_MAP'};

    }

    public function getDispatcher(string $event)
    {
        return $this->appContainer->get($this->eventMap[$event]);
    }


}