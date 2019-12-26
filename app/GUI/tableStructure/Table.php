<?php


namespace App\GUI\tableStructure;


use App\GUI\components\computers\SizeComputer;
use App\GUI\components\IOffset;
use App\GUI\components\ISize;
use App\GUI\ClickTransmitter;
use App\GUI\Color;
use App\GUI\Debug;
use App\GUI\helpers\TVisualAggregator;
use Gui\Components\VisualObjectInterface;
use function App\GUI\{getColor, color, height, top, left, width};

class Table implements IOffset, ISize
{
    use TVisualAggregator;

    private $currentRow;
    private $defaultColor;
    private $rows = [];
    private $parentRow;

    private $offsets = [];
    private $sizes = [];

    public function __construct(array $offsets, array $sizes, $defaultColor = Color::BLACK)
    {
        $this->currentRow = new CellRow($offsets, $sizes);
        $this->defaultColor = $defaultColor;
        $this->offsets = $offsets;
        $this->sizes = $sizes;
    }

    public function getRow(): CellRow
    {
        return $this->currentRow;
    }

    public function getRowById($key): CellRow
    {
        return $this->rows[$key];
    }

    public function rowCount(): int
    {
        return count($this->rows);
    }


    protected function getVisualComponents(): array
    {
        return $this->rows;
    }


    public function unsetRow($key): self
    {
        $this->rowsUpTo($key);
        unset($this->rows[$key]);
        return $this;
    }

    public function getSizes(): array
    {
        return $this->getRow()->getSizes();
    }

    public function getOffsets(): array
    {
        return $this->getRow()->getOffsets();
    }

    public function addCell(array $classes, array $additions, ?array $customSizes = null): VisualObjectInterface
    {
        $sizes =  $this->getRow()->getSizes();
        $sizes = $customSizes ?? $sizes;
        getColor($additions) ?: $additions[Color::KEY] = $this->defaultColor;
        return $this->getRow()->addCell($classes, $additions,null, $sizes);
    }


    public function newRow(string $key, $data): CellRow
    {
        [$offsets, $sizes] = [$this->getOffsets(), $this->getSizes()];
        $offsets[IOffset::LEFT] = left($this->offsets);
        $offsets[IOffset::TOP] += height($sizes);
        $this->currentRow = new CellRow($offsets, $sizes, $this);
        $this->rows[$key] = $this->getRow();
        $this->getRow()->setData($data);
        return $this->getRow();
    }

    public function beginCompositeCell(array $classes, string $compositeColor, ?array $newOffsets = null, ?array $compSizes = null, ?int $partsCount = null): VisualObjectInterface
    {
        [$prevOffsets, $prevSizes] = [$this->getOffsets(), $this->getSizes()];
        $compSizes = $compSizes ?? $this->sizes;
        $compositeShape = ($this->parentRow = $this->getRow())->addCell($classes, color($compositeColor),null, $compSizes);
        $newCellSizes[ISize::WIDTH] = $partsCount ? (width($compSizes) - 2 * left($newOffsets)) / $partsCount :  width($prevSizes) + 2 * left($newOffsets);
        $newCellSizes[ISize::HEIGHT] = height($prevSizes) - 2 * top($newOffsets);
        $newRowOffset = SizeComputer::plusOffsets($newOffsets, $prevOffsets);
        $this->currentRow = new CellRow($newRowOffset, $newCellSizes);
        return $compositeShape;
    }

    public function finishCompositeCell()
    {
        $this->parentRow->mergeCells($this->getRow());
        $this->currentRow = $this->parentRow;
        $this->parentRow = null;
    }

    private function rowsUpTo($key): self
    {
        $key += 1;
        $arr_keys = array_keys($this->rows);
        $offset = array_search($key, $arr_keys);
        $up = array_slice($this->rows, $offset);
        array_map(\Closure::fromCallable([$this, 'moveRow']), $up);
        return $this;
    }

    private function moveRow(CellRow $row)
    {
        $row->reduceTopOnOneHeight();
    }


}