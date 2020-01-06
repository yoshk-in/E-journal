<?php

namespace App\GUI;

use App\GUI\components\ISize;
use App\GUI\components\IOffset;
use App\GUI\components\IText;
use App\GUI\grid\style\BasicVisualStyle;
use App\GUI\grid\style\Style;



function sizeStyle(BasicVisualStyle $style, ?int $width = 0, ?int $height = 0): BasicVisualStyle
{
    $style->width = $width;
    $style->height = $height;
    return $style;
}

function offsetStyle(BasicVisualStyle $style, ?int $left = 0, ?int $top = 0): BasicVisualStyle
{
    $style->left = $left;
    $style->top = $top;
    return $style;
}

function cellStyle(BasicVisualStyle$style, $left, $top, $width, $height): BasicVisualStyle
{
    return sizeStyle(offsetStyle($style, $left, $top), $width, $height);
}


function textStyle(Style $style, $text = null, ?string $fontColor = null, ?int $fontSize = null): Style
{
    $style = $style ?? new Style();
    $style->value = $text ?? '';
    is_null($fontColor) ?: $style->fontColor = $fontColor;
    is_null($fontSize) ?: $style->fontSize = $fontSize;
    return $style;
}

function colorStyle(Style $style, ?string $color = null, ?string $backgroundColor = null, ?string $borderColor = null): Style
{
    is_null($color) ?: $style->color = $color;
    is_null($backgroundColor) ?: $style->backgroundColor = $backgroundColor;
    is_null($borderColor) ?: $style->borderColor = $borderColor;
    return $style;
}






