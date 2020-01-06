<?php


namespace App\GUI;


use App\GUI\components\Cell;
use App\GUI\components\WrapVisualObject;
use App\GUI\tableStructure\TableRow;

abstract class ClickHandler
{

    abstract public function handle(TableRow $emitter);
    abstract public function selectCell(Cell $cell);
    abstract public function unselectCell(Cell $cell);
    abstract public function removeSelectedCells();
    abstract public function handleInputNumber(WrapVisualObject $inputNumber);
    abstract public function areSelectedCellsExists(): bool;
}