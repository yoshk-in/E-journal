<?php


namespace App\GUI;

use Gui\Components\Div;
use Gui\Components\VisualObjectInterface;

class CompositeTable extends Div
{
    private $rowCount = 0;
    private $columnCount = 0;
    private $cellWidth = 100;
    private $cellHeight = 100;
    private $top = 20;
    private $left = 20;
    private $totalColumns;

    public function addRow( $obj)
    {
        $obj->setTop($this->top + $this->rowCount * $this->cellHeight)
            ->setLeft($this->left + $this->columnCount * $this->cellWidth)
            ->setWidth($this->cellWidth)
            ->setHeight($this->cellHeight);
        $this->appendChild($obj);
        ++$this->columnCount;
        return $this;
    }

    public function nextRow()
    {
        if ($this->columnCount > $this->totalColumns) {
            $this->totalColumns = $this->columnCount;
        }
        $this->columnCount = 0;
        ++$this->rowCount;
        $this->fixSize();
        return $this;
    }


    public function getTotalColumns()
    {
        return $this->totalColumns;
    }


    protected function fixSize()
    {
        $this->setWidth($this->cellWidth * $this->totalColumns)
            ->setHeight($this->cellHeight * $this->rowCount);
    }

}