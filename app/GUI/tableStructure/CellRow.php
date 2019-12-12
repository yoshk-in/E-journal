<?php


namespace App\GUI\tableStructure;


use App\GUI\components\assertion\ComponentIndexAssertion;
use App\GUI\components\IOffset;
use App\GUI\components\ISize;
use App\GUI\components\Cell;
use App\GUI\Color;
use App\GUI\factories\WrappingVisualObjectFactory;
use App\GUI\IVisualClass;
use Gui\Components\VisualObjectInterface;
use function App\GUI\{unsetKeys, width, getColor};

class CellRow implements IOffset, ISize
{
    private $offset;
    private $activeCell;
    private $activeColor;
    private $owner;
    private $data;
    private $cells = [];
    private $block = false;

    private $offsets = [];
    private $sizes = [];
    private $borderColor;

    private $cellFactory;


    public function __construct(array $offsets, array $sizes, $owner = null, $borderColor = Color::WHITE, $cellFactory =  WrappingVisualObjectFactory::class)
    {
        $this->offsets = $offsets;
        $this->sizes = $sizes;
        $this->owner = $owner;
        $this->borderColor = $borderColor;
        $this->cellFactory = $cellFactory;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function addCell(array $classes, array $additions, ?array $offsets = null, ?array $sizes = null)
    {
        $offsets = $offsets ?? $this->offsets;
        $sizes = $sizes ?? $this->sizes;
        $additions[Color::BORDER] = $this->borderColor;
        $additions[Color::BACKGROUND] = getColor($additions);
        ComponentIndexAssertion::check($offsets, $sizes, $additions);
        $cell = $this->cellFactory::create(
            $this->getClass($classes, IVisualClass::MAIN) ?? $classes[0]
            , $offsets,
            $sizes,
            $additions,
            $this->getClass($classes, IVisualClass::WRAP)
        );
        !isset($classes[IVisualClass::NEST]) ?: $this->nest($cell, $classes[IVisualClass::NEST] , $additions);
        $this->captureCell($cell);
        $this->offsets[IOffset::LEFT] += width($sizes);
        return $cell;
    }

    protected function getClass(array $classes, string $index): ?string
    {
        if (isset($classes[$index])) return $classes[$index];
        return null;
    }


    public function nest(VisualObjectInterface $cell, string $nestingClass, array $additions)
    {
        $cell->nest($nestingClass, unsetKeys($additions, [Color::KEY, Color::BORDER, Color::BACKGROUND]));
    }

    public function getOffsets(): array
    {
        return $this->offsets;
    }

    public function getSizes(): array
    {
        return $this->sizes;
    }

    public function setVisible(bool $bool)
    {
       $this->recursiveCallOnCells(__FUNCTION__, [$bool]);
    }

    protected function recursiveCallOnCells(string $methodName, array $args): self
    {
        array_map(function ($cell) use ($args, $methodName) {
            $cell->$methodName(...$args);
        }, $this->cells);
        return $this;
    }

    public function executeCallOnAllCells(\Closure $call): self
    {
        array_map(\Closure::fromCallable($call()), $this->cells);
        return $this;
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


    public function setData($data): void
    {
        $this->data = $data;
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


    public function mergeCells(CellRow $row)
    {
        $newCells = $row->getCells();
        $this->cells = array_merge($this->cells, $newCells);
        array_map(\Closure::fromCallable([$this, 'setCellOwnerThis']), $newCells);
    }

    private function setCellOwnerThis($cell)
    {
        $cell->setOwner($this);
    }

    public function reduceTopOnOneHeight()
    {
        $this->offset[IOffset::TOP] -= $this->sizes[ISize::HEIGHT];
        array_map(\Closure::fromCallable([$this, 'reduceElementTop']), $this->cells);
    }

    private function reduceElementTop($el)
    {
        $el->setTop($el->getTop() - $this->sizes[ISize::HEIGHT]);
    }


    private function keyWithoutHeadCell(int $key): int
    {
        return $key + 1;
    }


}