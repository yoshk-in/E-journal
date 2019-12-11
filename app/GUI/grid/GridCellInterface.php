<?php


namespace App\GUI\grid;



interface GridCellInterface
{
    public function toRight(GridCellInterface $rightCell): self;

    public function toDown(GridCellInterface $bottomCell): self;

    public function create(string $direction, Grid $grid, int $griCellId);

    public function getComponent();

}