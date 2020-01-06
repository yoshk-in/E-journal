<?php


namespace App\infoManager;

use App\base\AbstractRequest;
use App\base\AppMsg;
use App\CLI\render\event\Info;
use App\CLI\render\event\Move;
use App\CLI\render\event\NotFound;
use App\CLI\render\event\RangeInfo;
use App\events\Event;
use App\GUI\response\NotFoundResponseDispatcher;
use App\GUI\ResponseDispatcher;
use Psr\Container\ContainerInterface;


class DispatchResolver
{
    private ContainerInterface $appContainer;

    const CLI_MAP = [
        Event::PROCEDURE_CHANGE_STATE   => Move::class,
        AppMsg::PRODUCT_INFO            => Info::class,
        AppMsg::RANGE_INFO              => RangeInfo::class,
        AppMsg::NOT_FOUND               => NotFound::class
    ];




    public function __construct(ContainerInterface $appContainer)
    {
        $this->appContainer = $appContainer;

    }

    public function getDispatcher(string $event)
    {
        return $this->appContainer->get(self::CLI_MAP[$event]);
    }


}