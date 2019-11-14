<?php


namespace App\GUI;



use App\GUI\components\LabelWrapper;
use App\GUI\components\Cell;

class CellRow
{
    private $rowHeight;
    private $cellWidth;
    private $startTop;
    private $top;
    private $startLeft;
    private $offset;
    private $activeCell;
    private $activeColor;
    private $owner;
    private $data;
    private $cells = [];
    private $labels = [];
    private $block = false;



    public function __construct(int $startTop, int $startLeft, $rowHeight, $cellWidth, $owner = null)
    {
        $this->startTop = $startTop;
        $this->top = $startTop;
        $this->startLeft = $startLeft;
        $this->offset = $startLeft;
        $this->rowHeight = $rowHeight;
        $this->cellWidth = $cellWidth;
        $this->owner = $owner;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function addCell(string $color)
    {
        $shape = $this->addCellByWidth($this->cellWidth, $color);
        return $shape;
    }


    public function addCellByWidth($width, string $color)
    {
        $cell = (new Cell([
            'left' => $this->offset,
            'top' => $this->top,
            'width' => $width,
            'height' => $this->rowHeight,
            'backgroundColor' => $color,
            'borderColor' => Color::WHITE
        ]));
        $this->captureCell($cell);

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

    public function setVisible(bool $bool)
    {
        $this->executeCallOnAllEls(function ($el) use ($bool)
        {
            $el->setVisible($bool);
        });
    }

    protected function executeCallOnAllEls(\Closure $call)
    {
        foreach ($this->cells as $cell)
        {
            $call($cell);
        }
        foreach ($this->labels as $label)
        {
            $call($label);
        }
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

    public function captureCell(Cell $shape)
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

    public function mergeCellsAndLabels(CellRow $row)
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
        $this->top -= $this->rowHeight;
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