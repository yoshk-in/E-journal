<?php


namespace App\GUI;


use App\GUI\components\Cell;

abstract class ClickHandler
{

    abstract public function handle(Cell $emitter);

}