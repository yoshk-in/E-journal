<?php


namespace App\GUI\grid;



use App\GUI\grid\traits\TGridCellConstructor;

class GridCell extends AbstractGridCell
{
    use TGridCellConstructor, TCreateGridCell;


    public function getId(): int
    {
        return $this->cellId;
    }

}