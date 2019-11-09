<?php


namespace App\GUI;


use Gui\Components\VisualObject;

abstract class ClickHandler
{

    abstract public function handle(Cell $emitter, string $prevColor);

}