<?php


namespace App\GUI\tableStructure;


use App\GUI\Color;
use App\GUI\components\Cell;
use App\GUI\components\computer\StyleComputer;
use App\GUI\grid\style\Style;
use App\GUI\grid\traits\THierarchy;
use App\GUI\UserActionMng;
use Gui\Components\InputText;
use Gui\Components\Label;
use Gui\Components\Shape;
use function App\GUI\colorStyle;
use function App\GUI\sizeStyle;

class CellStyleInitializer
{

    private UserActionMng $clicked;
    const CLICK = 'mousedown';
    const ADD_NUMBER = 'change';
    private Style $generalStyle;

    public function __construct(UserActionMng $clicked)
    {
        $this->clicked = $clicked;
        $this->initCommonCellStyle();
    }


    protected function initCommonCellStyle()
    {
        $cellStyle = sizeStyle(new Style(Shape::class, Cell::class), 100, 50,);
        $cellStyle->on = [self::CLICK, fn(Cell $cell) => $this->clicked->handleRow($cell->getRow())];
        colorStyle($cellStyle, null, Color::BLACK, Color::WHITE);
        $this->generalStyle = $cellStyle;
    }

    public function getCommonCellStyle(): Style
    {
        $style = clone $this->generalStyle;
        $style->defaultBorderColor = Color::WHITE;
        return $style;
    }

    public function getCompositeCellStyle(): Style
    {
        $compositeStyle = clone $this->generalStyle;
        $compositeStyle->padding = 10;
        return $compositeStyle->defer('width', fn(int $partsCount, int $partsWidth) => $partsWidth * $partsCount);
    }

    public function getTextCellStyle(): Style
    {
        $textCellStyle = clone $this->generalStyle;
        $textCellStyle->child(colorStyle($labelStyle = new Style(Label::class), Color::WHITE));
        //on parts click emits parent click
        $labelStyle->on = [self::CLICK, fn(THierarchy $label) => $label->getParent()->fire(self::CLICK)];
        $create = $labelStyle->createCall;
        $labelStyle->createCall = fn(Style $labelS) => ($create)(StyleComputer::alignCenter($labelS->parent, $labelS));
        return $textCellStyle;
    }

    public function getInputCellStyle(): Style
    {
        $inputNumberCellStyle = clone $this->generalStyle;
        $inputNumberCellStyle->child($inputS = sizeStyle(new Style(InputText::class), 80, 30,));
        $inputS->on = [self::ADD_NUMBER, fn(Cell $cell) => $this->clicked->handleInputNumber($cell)];
        $create = $inputS->createCall;
        $inputS->createCall = fn(Style $inputS) => ($create)(StyleComputer::alignCenter($inputS->parent, $inputS));
        return  $inputNumberCellStyle;
    }
}