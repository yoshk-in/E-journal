<?php


namespace App\events;


class Channel
{
    protected static int $counter = 0;
    protected int $number;
    protected string $wave;
    protected array $channelSubscribers = [];

    public function __construct(string $wave)
    {
        $this->wave = $wave;
        $this->number = self::$counter++;
    }

    public function subscribeOn($subscriber)
    {
        $this->channelSubscribers[] = $subscriber;
    }
}