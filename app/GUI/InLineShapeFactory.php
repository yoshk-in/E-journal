<?php


namespace App\GUI;


use Gui\Components\Div;
use Gui\Components\Label;
use Gui\Components\Shape;
use Gui\Components\VisualObjectInterface;

class InLineShapeFactory
{
    private $rowHeight = 50;
    private $cellWidth = 100;
    private $startTop = 20;
    private $top;
    private $startLeft = 20;
    private $offset;

    private $rowCount = 0;

    public function __construct(int $startTop, int $startLeft, $rowHeight, $cellWidth)
    {
        $this->startTop = $startTop;
        $this->top = $startTop;
        $this->startLeft = $startLeft;
        $this->offset = $startLeft;
        $this->rowHeight = $rowHeight;
        $this->cellWidth = $cellWidth;
    }

    public function addInRow(string $color)
    {
        $shape = $this->addWithWidth($this->cellWidth, $color);
        return $shape;
    }

    public function addWithWidth($width, string $color)
    {
        $shape = (new Shape([
            'left' => $this->offset,
            'top' => $this->top,
            'width' => $width,
            'height' => $this->rowHeight,
            'backgroundColor' => $color
        ]));

        $this->offset += $width;
        return $shape;
    }


    public function newRow()
    {
        $this->offset = $this->startLeft;
        $this->top += $this->rowHeight;
        ++$this->rowCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getTop(): int
    {
        return $this->top;
    }

    /**
     * @return int
     */
    public function getCellWidth(): int
    {
        return $this->cellWidth;
    }

    /**
     * @return int
     */
    public function getRowHeight(): int
    {
        return $this->rowHeight;
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }


}