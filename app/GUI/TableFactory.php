<?php


namespace App\GUI;


use Gui\Components\Label;

class TableFactory
{
    private $shapeFactory;
    private $textFactory;
    private $wideCellWidth;
    private $defaultColor;
    private $fontSize;
    private $click;
    private $left;
    private $rows = [];
    private $parentFactory;


    public function __construct(int $startTop, int $startLeft, $rowHeight, $cellWidth, $wideCellWidth, MouseMng $click, $defaultColor = Color::BLACK, $fontSize = 10)
    {
        $this->shapeFactory = new RowCellFactory($startTop, $this->left = $startLeft, $rowHeight, $cellWidth);
        $this->textFactory = LabelFactory::class;
        $this->wideCellWidth = $wideCellWidth;
        $this->defaultColor = $defaultColor;
        $this->fontSize = $fontSize;
        $this->click = new ClickTransmitter($click);
    }

    public function getCurrentRow(): RowCellFactory
    {
        return $this->shapeFactory;
    }

    public function getWidth()
    {
        [ , , $width] = $this->shapeFactory->getSizes();
        return $width;
    }


    public function addTextShape(string $text, ?string $color = null): array
    {
        $sizes = $this->shapeFactory->getSizes();
        $shape = $this->shapeFactory->create($color ?? $this->defaultColor, $this->parentFactory ?? null);
        $text = $this->textInMiddle($text, ...$sizes);
        return [$text, $shape];
    }


    public function addWideTextShape(string $text)
    {
        [, $height, $left, $top] = $this->shapeFactory->getSizes();
        $this->shapeFactory->createByWidth($this->wideCellWidth, $this->defaultColor);
        $this->textInMiddle($text, $this->wideCellWidth, $height, $left, $top);
    }

    public function newRow(string $key, $data)
    {
        [$width, $height, , $top] = $this->shapeFactory->getSizes();
        $this->shapeFactory = new RowCellFactory($top + $height, $this->left, $height, $width);
        $this->rows[$key][] = $this->shapeFactory;
        $this->shapeFactory->setData($data);
    }

    public function addClickTextCell(string $text, string $color): Cell
    {
        [$text, $shape] = $this->addTextShape($text, $color);
        $this->click->fromTo($color, $text, $shape);
        return $shape;
    }


    public function beginCompositeCell(string $compositeColor, int $count, int $topOffset = 10, int $leftOffset = 10)
    {
        [, $height, $left, $top] = $this->shapeFactory->getSizes();
        $this->parentFactory = $this->shapeFactory;
        $compositeShape = $this->shapeFactory->createByWidth($this->wideCellWidth, $compositeColor);

        $this->click->on($compositeShape, $compositeColor);

        $this->rows[array_key_last($this->rows)][] = $this->shapeFactory = new RowCellFactory(
            $top + $topOffset,
            $left + $leftOffset,
            $height - 2 * $topOffset,
            $this->wideCellWidth / $count - $leftOffset / 2
        );
        return $compositeShape;
    }

    public function finishCompositeCell()
    {
        $this->shapeFactory = $this->parentFactory;
        $this->parentFactory = null;
    }

    public function addClickCell(string $color): Cell
    {
        $shape = $this->shapeFactory->create($color);
        $this->click->on($shape, $color);
        return $shape;
    }




    private function textInMiddle($text, $width, $height, $left, $top): Label
    {
        return LabelFactory::create(
            $text,
            $this->fontSize,
            $word_width = 0.73 * $this->fontSize * mb_strlen($text),
            $word_height = 2 * $this->fontSize,
            $top + ($height - $word_height) / 2,
            $left + ($width - $word_width) / 2
        );
    }


}