<?php


namespace App\events;


interface IEventType
{

    const START = 1;
    const END = 2;
    const ANY_MOVING = 3;
    const REPORT = 4;
    const ANY = 5;

    const PRODUCT_HAS_BEEN_SET = 6;

}