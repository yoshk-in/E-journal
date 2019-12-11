<?php


namespace App\GUI\components\traits;


use App\GUI\tableStructure\CellRow;

trait TOwnerable
{
    private $owner;

    public function getOwner(): CellRow
    {
        return $this->owner;
    }

    public function setOwner(CellRow $owner): void
    {
        $this->owner = $owner;
    }

}