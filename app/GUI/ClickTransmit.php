<?php


namespace App\GUI;


use Gui\Components\VisualObjectInterface;

class ClickTransmit
{
    static private $clickMng = MouseManger::class;

    public static function fromTo(VisualObjectInterface $from, VisualObjectInterface $to)
    {
        $from->on('mousedown', self::clickHandler($to));
        $to->on('mousedown', self::clickHandler($to));
    }

    public static function clickHandler($emitter)
    {
        return function () use ($emitter) {
            self::$clickMng::getHandler()::handle($emitter);
        };
    }

    public static function on(VisualObjectInterface $object)
    {
        $object->on('mousedown', self::clickHandler($object));
    }

}