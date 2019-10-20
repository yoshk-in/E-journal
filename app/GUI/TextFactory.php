<?php


namespace App\GUI;


use Gui\Components\Label;
use Gui\Components\Shape;
use Gui\Components\VisualObjectInterface;

class TextFactory
{
    static private $color = Color::BLACK;

    public static function inMiddle(VisualObjectInterface $shape, string $text): Label
    {
        $label = new Label(['text' => $text, 'fontColor' => self::$color]);
        $left = ($shape->getWidth() - $label->getWidth()) / 2;
        $top = ($shape->getHeight() - $label->getHeight()) / 2;
        $label->setTop($shape->getTop() + $top);
        $label->setLeft($shape->getLeft() + $left);
        return $label;
    }
}