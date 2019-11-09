<?php


namespace App\GUI;



class RowCellFactory
{
    private $rowHeight;
    private $cellWidth;
    private $startTop;
    private $top;
    private $startLeft;
    private $offset;
    private $activeCell;
    private $activeColor;
    private $data;
    private $cells = [];
    private $block = false;



    public function __construct(int $startTop, int $startLeft, $rowHeight, $cellWidth)
    {
        $this->startTop = $startTop;
        $this->top = $startTop;
        $this->startLeft = $startLeft;
        $this->offset = $startLeft;
        $this->rowHeight = $rowHeight;
        $this->cellWidth = $cellWidth;
    }

    public function create(string $color, $owner = null)
    {
        $shape = $this->createByWidth($this->cellWidth, $color, $owner);
        return $shape;
    }



    public function createByWidth($width, string $color, RowCellFactory $owner = null)
    {
        $shape = (new Cell([
            'left' => $this->offset,
            'top' => $this->top,
            'width' => $width,
            'height' => $this->rowHeight,
            'backgroundColor' => $color,
            'borderColor' => Color::WHITE
        ]));

        $owner ? $owner->addCell($shape) : $this->addCell($shape);

        $this->offset += $width;
        return $shape;
    }

    public function getSizes(): array
    {
        return [
            $this->cellWidth,
            $this->rowHeight,
            $this->offset,
            $this->top
        ];
    }

    public function getActiveCell(): Cell
    {
        return $this->activeCell;
    }

    public function getActiveColor(): string
    {
        return $this->activeColor;
    }


    public function getData()
    {
        return $this->data;
    }


    public function setData($data): void
    {
        $this->data = $data;
    }

    public function setActiveCell(int $key, string $color)
    {
        // first cell is just header product number not procedure so + 1
        $this->activeCell = $this->cells[$this->keyWithoutHeadCell($key)];
        $this->activeColor = $color;
    }

    public function addCell(Cell $shape)
    {
        $shape->setOwner($this);
        $this->cells[] = $shape;
    }


    public function getCell(int $key): Cell
    {
        return $this->cells[$this->keyWithoutHeadCell($key)];
    }

    public function getCells(): array
    {
        return $this->cells;
    }

    public function blockRow(bool $bool)
    {
        $this->block = $bool;
    }

    public function isBlock(): bool
    {
        return $this->block;
    }

    public function getHeadCell(): Cell
    {
        return $this->cells[0];
    }

    private function keyWithoutHeadCell(int $key): int
    {
        return $key + 1;
    }


}