<?php


namespace App\GUI\factories;



abstract class GuiComponentFactory
{
    abstract public static function create(string $class, array $offsets, array $sizes, array $additions);

}