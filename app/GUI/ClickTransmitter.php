<?php


namespace App\GUI;


use App\GUI\components\GuiComponentWrapper;
use App\GUI\components\Cell;

class ClickTransmitter
{
    private $clickMng;

    public function __construct(MouseHandlerMng $clickMng)
    {
        $this->clickMng = $clickMng;
    }

    public function fromTo(string $currentColor, GuiComponentWrapper $from, Cell $to)
    {
        $from->on('mousedown', $this->clickHandler($to, $currentColor));
        $to->on('mousedown', $this->clickHandler($to, $currentColor));
    }

    public function on(Cell $object, $currentColor)
    {
        $object->on('mousedown', $this->clickHandler($object, $currentColor));
    }

    public function clickHandler($emitter, $currentColor)
    {
        return function () use ($emitter, $currentColor) {
            $this->clickMng->getHandler()->handle($emitter, $currentColor);
        };
    }

}