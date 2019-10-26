<?php


namespace App\GUI;


use Gui\Components\VisualObjectInterface;

class ClickTransmit
{
    static private $clickMng = MouseManger::class;

    public static function fromTo(string $currentColor, VisualObjectInterface $from, VisualObjectInterface $to)
    {
        $from->on('mousedown', self::clickHandler($to, $currentColor));
        $to->on('mousedown', self::clickHandler($to, $currentColor));
    }

    public static function on(VisualObjectInterface $object, $currentColor)
    {
        $object->on('mousedown', self::clickHandler($object, $currentColor));
    }

    public static function clickHandler($emitter, $currentColor)
    {
        return function () use ($emitter, $currentColor) {
            self::$clickMng::getHandler()::handle($emitter, $currentColor);
        };
    }

}