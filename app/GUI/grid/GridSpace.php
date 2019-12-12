<?php


namespace App\GUI\grid;


use App\GUI\components\traits\TOwnerable;

class GridSpace extends AbstractGridCell
{
    use TOwnerable;

    protected function createCell(array $offsets): GridCellInterface
    {
        return $this;
    }

    public function getComponent()
    {
        return null;
    }

}