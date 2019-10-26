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
    private $left;


    public function __construct(int $startTop, int $startLeft, $rowHeight, $cellWidth, $wideCellWidth, $defaultColor = Color::BLACK, $fontSize = 10)
    {
        $this->shapeFactory = new RowShapeFactory($startTop, $this->left = $startLeft, $rowHeight, $cellWidth);
        $this->textFactory = LabelFactory::class;
        $this->wideCellWidth = $wideCellWidth;
        $this->defaultColor = $defaultColor;
        $this->fontSize = $fontSize;

    }

    public function setDataOnRow($data)
    {
        $this->shapeFactory->setData($data);
    }

    public function addTextCell(string $text): array
    {
        $sizes = $this->shapeFactory->getSizes();
        $shape = $this->shapeFactory->add($this->defaultColor);
        $text = $this->textInMiddle($text, ...$sizes);
        return [$text, $shape];
    }

    public function addWideTextCell(string $text)
    {
        [ , $height, $left, $top] = $this->shapeFactory->getSizes();
        $this->shapeFactory->addWithWidth($this->wideCellWidth, $this->defaultColor);
        $this->textInMiddle($text, $this->wideCellWidth, $height, $left, $top);
    }

    public function newRow()
    {
        [$width, $height, , $top] = $this->shapeFactory->getSizes();
        $this->shapeFactory = new RowShapeFactory($top + $height, $this->left, $height, $width);
    }

    public function addClickTextCell(string $text, string $color)
    {
        ClickTransmit::fromTo($color, ...$this->addTextCell($text));
    }

    public function addCompositeShape(
        \ArrayAccess $parts,
        int $count,
        \Closure $textCallback, //must return string for text part cells
        \Closure $colorCallback, //must return hex string color for all cells
        string $compositeColor,
        int $topOffset = 10,
        int $leftOffset = 10
    )
    {
        [ , $height, $left, $top] = $this->shapeFactory->getSizes();

        $shape = $this->shapeFactory->addWithWidth($this->wideCellWidth, $compositeColor, $this->shapeFactory);
        ClickTransmit::on($shape, $compositeColor);

        $partFactory = $partFact = new RowShapeFactory(
            $top + $topOffset,
            $left + $leftOffset,
            $height - 2 * $topOffset,
            $this->wideCellWidth / $count - $leftOffset / 2
        );

        foreach ($parts as $part) {
            $sizes = $partFactory->getSizes();
            $shape = $partFactory->add($colorCallback($part), $this->shapeFactory);
            ClickTransmit::fromTo(
                $colorCallback($part),
                $label = $this->textInMiddle($textCallback($part),...$sizes),
                $shape
            );
        }
    }

    public function addClickCell(string $color)
    {
        $shape = $this->shapeFactory->add($color);
        ClickTransmit::on($shape, $color);
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