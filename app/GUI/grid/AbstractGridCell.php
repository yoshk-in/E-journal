<?php


namespace App\GUI\grid;


use App\GUI\components\IOffset;

abstract class AbstractGridCell implements GridCellInterface, IOffset
{
    protected ?GridCellInterface $toRight;
    protected ?GridCellInterface $toDown;
    protected array $neighborCells = [];
    protected array $sizes;
    protected ?int $cellId;

    public function __construct(array $sizes)
    {
        $this->sizes = $sizes;
        $this->toRight = null;
        $this->toDown = null;
        $this->cellId = null;
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

    public function getChild(): \Iterator
    {
        foreach ($this->neighborCells as $direction => $cell) {
            yield [$direction => $cell];
        }
    }

    public function getSizes(): array
    {
        return $this->sizes;
    }


    public function create(string $direction, Grid $grid, int $parentCellId)
    {
        $this->cellId = $grid->pushCreate($parentCellId, \Closure::fromCallable([$this, 'createCell']), $this->sizes, $direction);
        $this->next($grid, $this->cellId);
    }


    protected function next(Grid $grid, int $parentCellId)
    {
        foreach ($this->neighborCells as $direction => $cell) {
            $cell->create($direction, $grid, $parentCellId);
        }
    }

    public function getId()
    {
        return $this->cellId;
    }

    abstract protected function createCell(array $offsets): GridCellInterface;
}