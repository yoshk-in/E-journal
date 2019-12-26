<?php


namespace App\GUI;


use App\GUI\components\Cell;

abstract class ClickHandler
{

    abstract public function handle(Cell $emitter);
    abstract public function selectCell(Cell $cell);
    abstract public function unselectCell(Cell $cell);
    abstract public function removeSelectedCells();
    abstract public function handleInputNumber(Cell $inputNumber);
    abstract public function areSelectedCellsExists(): bool;
}