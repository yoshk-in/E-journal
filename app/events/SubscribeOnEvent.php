<?php


namespace App\events;


class SubscribeOnEvent
{
    private string $entityClass;
    private ?string $entityEvent = null;
    private ?string $specialEntityObjectMark = null;

    public function __construct(string $entityClass, ?string $specialEntityObjectMark = null, ?string $entityEvent = null)
    {
    }
}