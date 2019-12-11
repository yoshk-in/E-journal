<?php


namespace App\GUI\grid;


use App\GUI\components\IOffset;

abstract class AbstractGridCell implements GridCellInterface, IOffset
{
    protected $toRight;
    protected $toDown;
    protected $neighborCells = [];
    protected $sizes;

    public function __construct(array $sizes)
    {
        $this->sizes = $sizes;
    }


    public function toRight(GridCellInterface $rightCell): GridCellInterface
    {
        $this->toRight = $rightCell;
        $this->neighborCells[IOffset::RIGHT] = $rightCell;
        return $this;
    }


    public function toDown(GridCellInterface $bottomCell): GridCellInterface
    {
        $this->toDown = $bottomCell;
        $this->neighborCells[IOffset::DOWN] = $bottomCell;
        return $this;
    }


    public function create(string $direction, Grid $grid, int $parentCellId)
    {
        $this->id = $grid->push($parentCellId, \Closure::fromCallable([$this, 'createCell']), $this->sizes, $direction);
        $this->next($grid, $this->id);
    }



    protected function next(Grid $grid, int $parentCellId)
    {
        foreach ($this->neighborCells as $direction => $cell) {
            $cell->create($direction, $grid, $parentCellId);
        }
    }

    abstract protected function createCell(array $offsets);
}