<?php


namespace App\events;


interface Event
{
    const ERROR = [

    ];

    const START = 'start';
    const END = 'end';
    const START_THEN_END = 'start and then end';
    const UNFINISHED_PRODUCTS_INFO = 'full info about unfinished products';
    const RANGE_PRODUCTS_INFO = 'full info about range concrete products';
}