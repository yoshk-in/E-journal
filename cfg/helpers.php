<?php

namespace cfg;

use App\events\IEventChannel;
use App\events\IEventType;
use App\events\OnClassEventSubscriber;
use DI\Definition\Helper\FactoryDefinitionHelper;
use Psr\Container\ContainerInterface;
use function DI\decorate;

function subscribe(array $onEvents): FactoryDefinitionHelper
{
    return decorate(function ($previous, ContainerInterface $container) use ($onEvents) {
        $channel = $container->get(IEventChannel::class);
        $channel->subscribe(OnClassEventSubscriber::create($previous, $onEvents));
        return $previous;
    });
}

function observe(): FactoryDefinitionHelper
{
    return decorate(function ($previous, ContainerInterface $container) {
        $container->get(IEventChannel::class)->attach($previous);
        return $previous;
    });
}