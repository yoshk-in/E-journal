<?php


namespace App\GUI\grid;


use App\GUI\components\IOffset;
use App\GUI\grid\style\Style;

abstract class AbstractGridCell
{
//    protected ?GridCellInterface $toRight = null;
//    protected ?GridCellInterface $toDown = null;
//    protected array $neighborCells = [];
    protected Style $style;
    protected ?int $cellId = null;

    public function __construct(Style $style)
    {
        $this->style = $style;
    }

    public function getStyle(): Style
    {
        return $this->style;
    }


//    public function toRight(GridCellInterface $rightCell): GridCellInterface
//    {
//        $this->toRight = $rightCell;
//        $this->neighborCells[IOffset::RIGHT] = $rightCell;
//        return $this;
//    }
//
//
//    public function toDown(GridCellInterface $bottomCell): GridCellInterface
//    {
//        $this->toDown = $bottomCell;
//        $this->neighborCells[IOffset::DOWN] = $bottomCell;
//        return $this;
//    }
//
//    public function getChildren(): \Iterator
//    {
//        foreach ($this->neighborCells as $direction => $cell) {
//            yield [$direction => $cell];
//        }
//    }
//
//    public function getSizes(): array
//    {
//        return $this->sizes;
//    }
//
//
//    public function create(string $direction, Grid $grid, int $parentCellId)
//    {
//        $this->cellId = $grid->pushCreate($parentCellId, \Closure::fromCallable([$this, 'createCell']), $this->sizes, $direction);
//        $this->next($grid, $this->cellId);
//    }
//
//
//    protected function next(Grid $grid, int $parentCellId)
//    {
//        foreach ($this->neighborCells as $direction => $cell) {
//            $cell->create($direction, $grid, $parentCellId);
//        }
//    }
//
//    public function getId()
//    {
//        return $this->cellId;
//    }
//
//    abstract protected function createCell(array $offsets): GridCellInterface;
}