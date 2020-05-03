<?php

namespace App\domain\productClassGen;

use App\events\IEventType;

/**
 * @Entity
 */
class Product1 extends \App\domain\AbstractProduct
{
    use \App\events\traits\TObservable;

    const NAME = 'НР381Б-02';

    public function event($event = IEventType::ANY)
    {
        $this->bubbleUpEvent((string)$event );
    }
}
