<?php


namespace App\GUI;


use Gui\Components\Div;
use Gui\Components\Label;
use Gui\Components\Shape;
use Gui\Components\VisualObjectInterface;

class InLineShapeFactory
{
    private  $rowHeight = 50;
    private  $cellWidth = 100;
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

    public function addInRow(string $color = Color::WHITE)
    {
        $shape = (new Shape())
            ->setLeft($this->offset)
            ->setTop($this->top)
            ->setWidth($this->cellWidth)
            ->setHeight($this->rowHeight)
            ->setBackgroundColor($color);
        $this->offset += $this->cellWidth;
        return $shape;
    }

    public function addWithWidth(string $color, int $width)
    {
        $shape = (new Shape())
            ->setLeft($this->offset)
            ->setTop($this->top)
            ->setWidth($width)
            ->setHeight($this->rowHeight)
            ->setBackgroundColor($color);
        $this->offset += $width;
        return $shape;
    }


    public function newLine()
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