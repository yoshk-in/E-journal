<?php


namespace App\GUI\tableStructure;


use App\GUI\components\Cell;
use App\GUI\components\IOffset;
use App\GUI\components\ISize;
use App\GUI\grid\style\RowStyle;
use App\GUI\grid\style\Style;
use App\GUI\grid\traits\THierarchy;
use App\GUI\helpers\TVisualAggregator;
use Gui\Components\VisualObjectInterface;

class TableRow implements IOffset, ISize
{
    use THierarchy, TVisualAggregator;

    private VisualObjectInterface $activeCell;
    private string $activeColor;
    private $data;
    private array $cells = [];
    private bool $block = false;

    private RowStyle $style;
    private int $leftOffset;

    private TableRow $attachingRow;


    public function __construct(RowStyle $style)
    {
        $this->leftOffset = $style->left;
        $this->style = clone $style;
        $this->attachingRow = $this->style->parentRow ?? $this;
        $this->parent = $this->style->table;
    }

    public function attachCell(VisualObjectInterface $object): self
    {
        $this->cells[] = $object;
        $object->setRow($this);
        return $this;
    }

    public function addCell(Style $style): VisualObjectInterface
    {
        $style->left = $this->leftOffset + $style->margin;
        $cell = $style->create();
        $this->leftOffset += $style->left;
        $this->attachingRow->attachCell($cell);
        return $cell;
    }


    protected function getClass(array $classes, string $index): ?string
    {
        if (isset($classes[$index])) return $classes[$index];
        return null;
    }

    public function getStyle(): RowStyle
    {
        return clone $this->style;
    }


    protected function getVisualComponents(): array
    {
        return $this->cells;
    }


    public function getActiveCell(): ?VisualObjectInterface
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


    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    public function activateCellById(int $key, string $color)
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


    public function reduceTopOnOneHeight()
    {
        $this->style->top -= $this->style->height;
        array_map(\Closure::fromCallable([$this, 'reduceElementTop']), $this->cells);
    }

    private function reduceElementTop($el)
    {
        $el->setTop($el->getTop() - $this->style->height);
    }


    private function keyWithoutHeadCell(int $key): int
    {
        return $key + 1;
    }


}