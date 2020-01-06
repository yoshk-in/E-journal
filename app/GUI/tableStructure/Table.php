<?php


namespace App\GUI\tableStructure;


use App\GUI\components\computer\StyleComputer;
use App\GUI\grid\style\RowStyle;
use App\GUI\grid\style\Style;
use App\GUI\helpers\TVisualAggregator;

class Table
{
    use TVisualAggregator;

    protected TableRow $currentRow;
    protected array $rows = [];
    protected int $rowDepth = 0;
    protected int $rowOrder = 0;
    protected array $dataRows = [];
    protected int $topOffset = 0;
    protected Style $currentCellStyle;
    protected RowStyle $style;
    protected TableRow $rootRow;
    public \Closure $createNewRowStyle;
    public \Closure $createNestingRowStyle;
    public TableRow $header;

    public function __construct(RowStyle $style)
    {
        $this->rootRow = $this->header = $this->createCurrentRow($style);
        $this->style = clone $style;
        $this->topOffset = $style->top;
        $this->createNewRowStyle = function (RowStyle $style, int $topOffset) {
            $style->top = $topOffset;
            $style->increaseLeftTopOn($style->margin);
        };
        $this->createNestingRowStyle = function (Style $parentCell, $attachToRow): RowStyle {
            ($newRowStyle = new RowStyle($this->currentCellStyle))
            ->increaseLeftTopOn($this->currentCellStyle->padding)
                ->parentRow = $attachToRow;
            return $newRowStyle;
        };
    }


    protected function createCurrentRow(RowStyle $style): TableRow
    {
        $style->table = $this;
        return $this->currentRow = new TableRow($style);
    }

    protected function createCurrentDataRow(RowStyle $style, $data): TableRow
    {
        return $this->createCurrentRow($style)->setData($data);
    }

    public function getCurrentRow(): TableRow
    {
        return $this->currentRow;
    }


    public function getRow(int $orderNumber, int $depthNumber): TableRow
    {
        return $this->rows[$depthNumber][$orderNumber];
    }


    public function rootRowCount(): int
    {
        return count($this->rows);
    }


    protected function getVisualComponents(): array
    {
        return $this->rows;
    }


    public function unsetRowByKey($key): self
    {
        [$depth, $order] = $this->dataRows[$key];
        $this->moveRowsUpTo($depth, $order);
        unset($this->rows[$depth][$order]);
        unset($this->dataRows[$key]);
        return $this;
    }

    public function addCell(Style $style): self
    {
        $style = clone $style;
        $style->top = $this->topOffset + $style->margin;
        $this->currentRow->addCell($this->currentCellStyle = $style);
        return $this;
    }

    public function newDataRow(string $userKey, $data): TableRow
    {
        $this->setUserKeyToRowPosition($userKey);
        return $this->newRow()->setData($data);
    }

    protected function setUserKeyToRowPosition($key): self
    {
        $this->dataRows[$key] = [$this->rowDepth, $this->rowOrder];
        return $this;
    }

    public function newRow(): TableRow
    {
        $style = $this->currentRow->getStyle();
        $newRowStyle = ($this->createNewRowStyle)(clone $style, $this->topOffset);
        return $this->rows[$this->rowDepth][++$this->rowOrder] = $this->createCurrentRow($newRowStyle);
    }

    public function nestInCellNewRow(\Closure $closure, ?TableRow $attachToRow = null): self
    {
        return $this->nestInCellRow(fn (RowStyle $style) => $this->createCurrentRow($style), $closure, $attachToRow);
    }

    public function nestInCellNewDataRow(\Closure $closure, string $key, $data, ?TableRow $attachToRow = null): self
    {
        $this->nestInCellRow(fn (RowStyle $style) => $this->createCurrentDataRow($style, $data), $closure, $attachToRow);
        return $this->setUserKeyToRowPosition($key);
    }

    protected function nestInCellRow(\Closure $createRowClosure, \Closure $nestingAction, ?TableRow $attachNestingCellsToRow = null): self
    {
        $newRowStyle = ($this->createNestingRowStyle)($this->currentCellStyle, $attachNestingCellsToRow);
        ++$this->rowDepth;
        $prevRow = $this->currentRow;
        $nestingAction($createRowClosure($newRowStyle), clone $this->currentCellStyle);
        $this->currentRow = $prevRow;
        --$this->rowDepth;
        return $this;
    }

    private function moveRowsUpTo(int $depth, int $order): self
    {
        $rowsMovingUpArr = array_slice($this->rows[$depth], ++$order);
        array_map(fn ($row) => $this->moveRow($row), $rowsMovingUpArr);
        return $this;
    }

    private function moveRow(TableRow $row)
    {
        $row->reduceTopOnOneHeight();
    }


}