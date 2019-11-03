<?php


namespace App\GUI;



class RowShapeFactory
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
//    private $childRows;
    private $cells = [];



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
        $shape = $this->createWithWidth($this->cellWidth, $color, $owner);
        return $shape;
    }

//    public function addChildRow(RowShapeFactory $row)
//    {
//        $this->childRows[] = $row;
//    }


    public function createWithWidth($width, string $color, RowShapeFactory $owner = null)
    {
        $shape = (new Shape([
            'left' => $this->offset,
            'top' => $this->top,
            'width' => $width,
            'height' => $this->rowHeight,
            'backgroundColor' => $color,
            'borderColor' => Color::WHITE
        ]));
        $this->cells[] = $shape;
        $shape->setId(array_key_last($this->cells));

        $owner ? $shape->setOwner($owner) && $owner->addCell($shape) /*&& $owner->addChildRow($this)*/ : $shape->setOwner($this) && $this->addCell($shape);

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

    public function getActiveCell(): ?Shape
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

    public function setActiveCell(Shape $cell, string $color)
    {
        $this->activeCell = $cell;
        $this->activeColor = $color;
    }

    public function addCell(Shape $shape)
    {
        $this->cells[] = $shape;
        $shape->setId(array_key_last($this->cells));
    }

    public function nextActiveCell(Shape $shape, string $color)
    {
        $next = $this->cells[$shape->getId() + 1] ?? $shape;
        $this->setActiveCell($next, $color);
    }


}