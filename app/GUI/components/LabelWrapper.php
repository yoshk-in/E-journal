<?php


namespace App\GUI\components;



use Gui\Components\Label;

class LabelWrapper extends GuiComponentWrapper
{

    protected function componentClass(): string
    {
        return Label::class;
    }
}