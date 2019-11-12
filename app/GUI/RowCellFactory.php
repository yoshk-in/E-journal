<?php


namespace App\GUI;



use App\GUI\components\LabelWrapper;
use Gui\Components\Label;
use App\GUI\components\Cell;

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
    private $labels = [];
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

    public function create(string $color)
    {
        $shape = $this->createByWidth($this->cellWidth, $color);
        return $shape;
    }


    public function createByWidth($width, string $color)
    {
        $cell = (new Cell([
            'left' => $this->offset,
            'top' => $this->top,
            'width' => $width,
            'height' => $this->rowHeight,
            'backgroundColor' => $color,
            'borderColor' => Color::WHITE
        ]));
        $this->addCell($cell);

        $this->offset += $width;
        return $cell;
    }

    public function addLabelForLastCell(LabelWrapper $label)
    {
        $this->labels[array_key_last($this->cells)] = $label;
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

    public function getActiveCell(): ?Cell
    {
        return $this->activeCell;
    }

    public function getActiveColor(): ?string
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

    public function setActiveCellById(int $key, string $color)
    {
        // first cell is just header product number not procedure so + 1
        $this->activeCell = $this->cells[$this->keyWithoutHeadCell($key)];
        $this->activeColor = $color;
    }

    public function setActiveCell(Cell $cell, string $color)
    {
        $this->activeCell = $cell;
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

    public function getCellsAndLabels(): array
    {
        return [$this->cells, $this->labels];
    }

    public function mergeCellsAndLabels(RowCellFactory $row)
    {
        $cellsAndLabels = $row->getCellsAndLabels();
        $this->cells = array_merge($this->cells, $cellsAndLabels[0]);
        $this->labels = array_merge($this->labels, $cellsAndLabels[1]);
        foreach ($cellsAndLabels[0] as $cell) {
            $cell->setOwner($this);
        }
    }

    public function reduceTopOnOneHeight()
    {
        foreach ($this->cells as $cell)
        {
            $cell->setTop($cell->getTop() - $this->rowHeight);
        }

        foreach ($this->labels as $label)
        {
            $label->setTop($label->getTop() - $this->rowHeight);
        }
    }


    private function keyWithoutHeadCell(int $key): int
    {
        return $key + 1;
    }


}