<?php


namespace App\GUI;


use Gui\Components\Label;

class Table
{
    private $shapeFactory;
    private $textFactory;
    private $wideCellWidth;
    private $defaultColor;
    private $fontSize;

    public function __construct(int $startTop, int $startLeft, $rowHeight, $cellWidth, $wideCellWidth, $defaultColor = Color::WHITE, $fontSize = 10)
    {
        $this->shapeFactory = new InLineShapeFactory($startTop, $startLeft, $rowHeight, $cellWidth);
        $this->textFactory = TextFactory::class;
        $this->wideCellWidth = $wideCellWidth;
        $this->defaultColor = $defaultColor;
        $this->fontSize = $fontSize;
    }

    public function addTextShape(string $text): array
    {
        //sizes for text Label
//        $top = $this->shapeFactory->getTop();
//        $left = $this->shapeFactory->getOffset();
//        $height = $this->shapeFactory->getRowHeight();
//        $width = $this->shapeFactory->getCellWidth();
        $sizes = $this->getCurrentOffsets();
        $shape = $this->shapeFactory->addInRow($this->defaultColor);
        $text = $this->textInMiddle($text, ...$sizes);
        return [$text, $shape];
    }

    public function addWideTextShape(string $text)
    {
        [$width, $height, $left, $top] = $this->getCurrentOffsets();
        $this->shapeFactory->addWithWidth($this->wideCellWidth, $this->defaultColor);
        $this->textInMiddle($text, $this->wideCellWidth, $height, $left, $top);
    }

    public function newLine()
    {
        $this->shapeFactory->newRow();
    }

    public function addClickTextShape(string $text)
    {
        ClickTransmit::fromTo(...$this->addTextShape($text));
    }

    public function addCompositeShape(
        \ArrayAccess $parts,
        int $count,
        \Closure $textCallback, //must return string for text part cells
        \Closure $colorCallback, //must return hex string color for all cells
        int $topOffset = 10,
        int $leftOffset = 10,
        string $CompositeColor = Color::WHITE
    )
    {
        [$width, $height, $left, $top] = $this->getCurrentOffsets();

        $shape = $this->shapeFactory->addWithWidth($this->wideCellWidth, $CompositeColor);
        ClickTransmit::on($shape);

        $partFactory = $partFact = new InLineShapeFactory(
            $top + $topOffset,
            $left + $leftOffset,
            $height - 2 * $topOffset,
            $this->wideCellWidth / $count - $leftOffset / 2
        );

        foreach ($parts as $part) {
            $sizes = [$partFactory->getCellWidth(), $partFactory->getRowHeight(), $partFactory->getOffset(), $partFactory->getTop()];
            $shape = $partFactory->addInRow($colorCallback($part));
            ClickTransmit::fromTo($this->textInMiddle($textCallback($part),...$sizes), $shape);

        }
    }

    public function addClickShape(string $color)
    {
        $shape = $this->shapeFactory->addInRow($color);
        ClickTransmit::on($shape);
    }

    private function getCurrentOffsets(): array
    {
        return [
            $this->shapeFactory->getCellWidth(),
            $this->shapeFactory->getRowHeight(),
            $this->shapeFactory->getOffset(),
            $this->shapeFactory->getTop(),
        ];
    }

    private function textInMiddle($text, $width, $height, $left, $top): Label
    {
        return TextFactory::inMiddle(
            $text,
            $this->fontSize,
            $word_width = 0.7 * $this->fontSize * mb_strlen($text),
            $word_height = 2 * $this->fontSize,
            $top + ($height - $word_height) / 2,
            $left + ($width - $word_width) / 2
        );
    }

}