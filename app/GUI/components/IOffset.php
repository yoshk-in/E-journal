<?php


namespace App\GUI\components;


interface IOffset
{
    const LEFT = 'left';
    const TOP = 'top';
    const RIGHT = 'right';
    const BOTTOM = 'bottom';
    const DOWN = 'down';
    const TO_RIGHT = self::LEFT;
    const TO_DOWN = self::TOP;
}