<?php


namespace App\GUI\factories;



use App\GUI\grid\style\Style;

abstract class GuiComponentFactory
{
    abstract public static function create(Style $style);

}