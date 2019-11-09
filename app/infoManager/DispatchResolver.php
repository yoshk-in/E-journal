<?php


namespace App\infoManager;

use App\base\AbstractRequest;
use App\base\AppMsg;
use App\CLI\render\event\Info;
use App\CLI\render\event\Move;
use App\CLI\render\event\NotFound;
use App\CLI\render\event\RangeInfo;
use App\GUI\response\NotFoundResponseDispatcher;
use App\GUI\ResponseDispatcher;
use Psr\Container\ContainerInterface;


class DispatchResolver
{
    private $appContainer;
    private $eventMap = [];

    static private $CLI_MAP = [
        AppMsg::ARRIVE          => Move::class,
        AppMsg::DISPATCH        => Move::class,
        AppMsg::INFO            => Info::class,
        AppMsg::RANGE_INFO      => RangeInfo::class,
        AppMsg::NOT_FOUND       => NotFound::class
    ];

    static private $GUI_MAP = [
        AppMsg::INFO            => ResponseDispatcher::class,
        AppMsg::DISPATCH        => ResponseDispatcher::class,
        AppMsg::ARRIVE          => ResponseDispatcher::class,
        AppMsg::NOT_FOUND       => NotFoundResponseDispatcher::class
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