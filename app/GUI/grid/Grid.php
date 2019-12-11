<?php


namespace App\GUI\grid;


use App\GUI\components\IOffset;
use App\GUI\components\ISize;
use function App\GUI\offset;
use function App\GUI\size;

class Grid implements IOffset, ISize
{
    private $grid = [];
    private $wrongKey = 'wrong offset or size array index name';
    private $startCell;
    private $offsets;
    private $cellCounter = -1;

    const DIRECTION_TO_OFFSET = [
          'right' => IOffset::LEFT,
          'down' => IOffset::TOP
    ];

    const OFFSET_TO_SIZE = [
        IOffset::LEFT => ISize::WIDTH,
        IOffset::TOP => ISize::HEIGHT
    ];

    public function __construct(GridCellInterface $cell, array $offsets = [IOffset::LEFT => 0, IOffset::TOP => 0])
    {
        $this->startCell = $cell;
        $this->offsets = $offsets;

        $this->grid[$this->cellCounter] = [$offsets, size(0,0) ];
        $this->startCell->create( 'right', $this, -1);
    }



    public function push(int $parentId, \Closure $createClosure, array $childSizes, string $direction): int
    {
        $key = self::DIRECTION_TO_OFFSET[$direction];
        [$offsets, $parentSizes] = $this->getCellSizes($parentId);
        $offsets[$key] = $this->value($offsets, $key) + $this->value($parentSizes, self::OFFSET_TO_SIZE[$key]);
        return $this->createCell($createClosure, $offsets, $childSizes);
    }


    private function createCell(\Closure $createClosure, array $offsets, array $sizes): int
    {
        $createClosure($offsets);
        $this->grid[++$this->cellCounter] = [$offsets, $sizes];
        return $this->cellCounter;
    }

    private function value(array $array, string $key)
    {
        assert(array_key_exists($key, $array), $this->wrongKey);
        return $array[$key];
    }


    private function getCellSizes(int $id): array
    {
        return $this->value($this->grid, (string) $id);
    }



}