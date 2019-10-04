<?php


namespace App\events;


use App\console\render\InfoFoundRender;

interface Event
{
    const ERROR = [

    ];

    const START = InfoFoundRender::class;
    const END = 'end';
    const PARTIAL_START = 'start and then end';
    const COMPOSITE_START = 'composite start';
    const COMPOSITE_END = 'composite end';
    const UNFINISHED_PRODUCTS_INFO = 'full info about unfinished products';
    const RANGE_PRODUCTS_INFO = 'full info about range concrete products';
}