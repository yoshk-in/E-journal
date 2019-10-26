<?php


namespace App\GUI;


use Gui\Components\Div;
use Gui\Components\Label;
use App\GUI\Shape;
use Gui\Components\VisualObjectInterface;

class RowShapeFactory
{
    private $rowHeight;
    private $cellWidth;
    private $startTop;
    private $top;
    private $startLeft;
    private $offset;
    private $notActiveColor;
    private $activeCell;
    private $cellCount = 0;
    private $activeColor;
    private $data;


    public function __construct(int $startTop, int $startLeft, $rowHeight, $cellWidth, string $notActiveColor = COLOR::GREEN)
    {
        $this->startTop = $startTop;
        $this->top = $startTop;
        $this->startLeft = $startLeft;
        $this->offset = $startLeft;
        $this->rowHeight = $rowHeight;
        $this->cellWidth = $cellWidth;
        $this->notActiveColor = $notActiveColor;
    }

    public function add(string $color, $owner = null)
    {
        $shape = $this->addWithWidth($this->cellWidth, $color, $owner);
        ++$this->cellCount;
        if (is_null($this->activeCell) && ($this->cellCount > 1)) {
            ($color === $this->notActiveColor) ?: ($this->activeCell = $shape) && $this->activeColor = $color;
        }
        return $shape;
    }

    public function addWithWidth($width, string $color, $owner = null)
    {
        $shape = (new Shape([
            'left' => $this->offset,
            'top' => $this->top,
            'width' => $width,
            'height' => $this->rowHeight,
            'backgroundColor' => $color,
            'borderColor' => Color::WHITE
        ]));
        $shape->setOwner($owner ?? $this);

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


}