<?php


namespace App\events;


class EventChannel implements IEventChannel
{
    const FORKING_EVENTS = [
        IEventType::ANY_MOVING => [IEventType::START, IEventType::END],
        IEventType::ANY => [IEventType::REPORT, IEventType::START, IEventType::END],
    ];



    protected array $channels = [];

    public function __construct()
    {
        Event::connectToEventChannel($this);
    }

    public function update(Event $event)
    {
        $subscribers = ($this->channels[(string) $event] ??= []);
        foreach ($subscribers as $closure) {
            $closure($event->observable, $event->class);
        }
    }




    public function subscribe(\stdClass $on)
    {
        /** @var  $onEvents [$class, $event, $specialClassMark] */
        $callKey = $this->subscriberKey($on->closure);
        $call = [$callKey => $on->closure];
        foreach (self::getForkingChannelKey($on->event) as $channelKey) {
            $this->channels[$this->getChannelName([$on->observableClass, $channelKey])] = $call;
        }
    }

    protected function getChannelName(array $subChannels): string
    {
        $res = '';
        foreach ($subChannels as $subChannel) {
            $res .= $subChannel;
        }
        return $res;
    }

//    protected static function fillForkingChannels(array &$onEventProps): \Generator
//    {
//        foreach (self::$subChannelsMap as $propsId => $subChannel) {
//            yield from self::getForkingChannelKey($onEventProps[$propsId], $subChannel);
//        }
//    }


    protected static function getForkingChannelKey(int &$eventProp, array $subChannelMap = self::FORKING_EVENTS): \Generator
    {
        $subChannelMap = $subChannelMap[$eventProp] ?? [$eventProp];
        foreach ($subChannelMap as $subChannelKey) {
            yield $subChannelKey;
        }
    }



    public function unsubscribe($subscriber, string $event, string $type = self::DEFAULT_TYPE)
    {
        exit('todo');
        if (isset($this->channels[$event][$key = $this->subscriberKey($subscriber)])) {
            unset($this->channels[$event][$type][$key]);
        }
    }


    public static function subscriberKey($subscriber): string
    {
        return is_string($subscriber)? $subscriber : get_class($subscriber);
    }


}