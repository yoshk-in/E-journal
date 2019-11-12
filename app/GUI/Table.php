<?php


namespace App\GUI;


use App\GUI\components\LabelWrapper;
use Gui\Components\Label;
use App\GUI\components\Cell;
use App\GUI\factories\LabelFactory;

class Table
{
    private $currentRow;
    private $textFactory;
    private $wideCellWidth;
    private $defaultColor;
    private $fontSize;
    private $click;
    private $left;
    private $rows = [];
    private $parentRow;


    public function __construct(int $startTop, int $startLeft, $rowHeight, $cellWidth, $wideCellWidth, MouseMng $click, $defaultColor = Color::BLACK, $fontSize = 10)
    {
        $this->currentRow = new RowCellFactory($startTop, $this->left = $startLeft, $rowHeight, $cellWidth);
        $this->textFactory = LabelFactory::class;
        $this->wideCellWidth = $wideCellWidth;
        $this->defaultColor = $defaultColor;
        $this->fontSize = $fontSize;
        $this->click = new ClickTransmitter($click);
    }

    public function getCurrentRow(): RowCellFactory
    {
        return $this->currentRow;
    }

    public function getRow($key): RowCellFactory
    {
        return $this->rows[$key];
    }

    public function unsetRow($key)
    {
        $this->rowsUpTo($key);
        unset($this->rows[$key]);
    }

    public function getWidth()
    {
        [ , , $width] = $this->currentRow->getSizes();
        return $width;
    }


    public function addTextCell(string $text, ?string $color = null): array
    {
        $sizes = $this->currentRow->getSizes();
        $shape = $this->currentRow->create($color ?? $this->defaultColor);
        $text = $this->textInMiddle($text, ...$sizes);
        return [$text, $shape];
    }


    public function addWideTextCell(string $text)
    {
        [, $height, $left, $top] = $this->currentRow->getSizes();
        $this->currentRow->createByWidth($this->wideCellWidth, $this->defaultColor);
        $this->textInMiddle($text, $this->wideCellWidth, $height, $left, $top);
    }

    public function newRow(string $key, $data): RowCellFactory
    {
        [$width, $height, , $top] = $this->currentRow->getSizes();
        $this->currentRow = new RowCellFactory($top + $height, $this->left, $height, $width);
        $this->rows[$key] = $this->currentRow;
        $this->currentRow->setData($data);
        return $this->currentRow;
    }

    public function addClickTextCell(string $text, string $color): Cell
    {
        [$text, $shape] = $this->addTextCell($text, $color);
        $this->click->fromTo($color, $text, $shape);
        return $shape;
    }


    public function beginCompositeCell(string $compositeColor, int $count, int $topOffset = 10, int $leftOffset = 10)
    {
        [, $height, $left, $top] = $this->currentRow->getSizes();
        $this->parentRow = $this->currentRow;
        $compositeShape = $this->currentRow->createByWidth($this->wideCellWidth, $compositeColor);

        $this->click->on($compositeShape, $compositeColor);

        $this->currentRow = new RowCellFactory(
            $top + $topOffset,
            $left + $leftOffset,
            $height - 2 * $topOffset,
            $this->wideCellWidth / $count - $leftOffset / 2
        );
        return $compositeShape;
    }

    public function finishCompositeCell()
    {
        $this->parentRow->mergeCellsAndLabels($this->currentRow);
        $this->currentRow = $this->parentRow;
        $this->parentRow = null;
    }

    public function addClickCell(string $color): Cell
    {
        $shape = $this->currentRow->create($color);
        $this->click->on($shape, $color);
        return $shape;
    }


    private function textInMiddle($text, $width, $height, $left, $top): LabelWrapper
    {
        $label = LabelFactory::create(
            $text,
            $this->fontSize,
            $word_width = 0.73 * $this->fontSize * mb_strlen($text),
            $word_height = 2 * $this->fontSize,
            $top + ($height - $word_height) / 2,
            $left + ($width - $word_width) / 2
        );
        $this->currentRow->addLabelForLastCell($label);
        return $label;
    }

    private function rowsUpTo($key)
    {
        $key += 1;
        $arr_keys = array_keys($this->rows);
        $offset = array_search($key, $arr_keys);
        $up = array_slice($this->rows, $offset);
        foreach ($up as $row) {
            $row->reduceTopOnOneHeight();
        }
    }


}