<?php

namespace App\domain\productClassGen;

use App\events\IEventType;

/**
 * @Entity
 */
class Product0 extends \App\domain\AbstractProduct
{
    use \App\events\traits\TObservable;

    const NAME = 'Г9';

    public function event($event = IEventType::ANY)
    {
        $this->bubbleUpEvent((string)$event );
    }
}
