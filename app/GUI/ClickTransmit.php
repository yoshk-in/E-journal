<?php


namespace App\GUI;


use Gui\Components\VisualObjectInterface;

class ClickTransmit
{
    private $clickStrategy;

    public function fromTo(VisualObjectInterface $from, VisualObjectInterface $to)
    {
        $from->on('mousedown', $this->clickStrategy->getClickHandler($to));
        $to->on('mousedown', $this->clickStrategy->getClickHandler($to));
    }

    public static function clickHandler($emitter)
    {
        return function () use ($emitter) {
            static $i = 0;
            $i++;
            if ($i % 2 === 0) {
                $emitter->setBackgroundColor(Color::GREEN);
            } else $emitter->setBackgroundColor(Color::YELLOW);
        };
    }

    public function setClickStrategy(ClickStrategy $strategy)
    {
        $this->clickStrategy = $strategy;
    }
}