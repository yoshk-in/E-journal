<?php


namespace App\GUI\components\computer;


use App\GUI\grid\style\Style;
use Gui\Components\Label;

class StyleComputer
{
    const COMPONENT_METHOD = [
        Label::class => 'textInMiddle'
    ];

    public static function alignCenter(Style $parentStyle, Style $childStyle): Style
    {
        $method = self::COMPONENT_METHOD[$parentStyle->guiComponentClass] ?? 'shapeInMiddle';
        return self::$method($parentStyle, $childStyle);
    }

    public static function textInMiddle(Style $parentStyle, Style $textStyle): Style
    {
        $word_width = round(0.73 * $textStyle->fontSize * mb_strlen($textStyle->value));
        $word_height = 2 * $textStyle->fontSize;
        $textStyle->top = $parentStyle->top + ($parentStyle->height - $word_height) / 2;
        $textStyle->left = $parentStyle->left + ($parentStyle->width - $word_width) / 2;
        return $textStyle;
    }

    public static function shapeInMiddle(Style $parentStyle, Style $childStyle): Style
    {
        $childStyle->top = $parentStyle->top + $childStyle->margin;
        $childStyle->left = $parentStyle->left + $childStyle->margin;
        $childStyle->width = $parentStyle->width + $childStyle->margin;
        return $childStyle;
    }



}