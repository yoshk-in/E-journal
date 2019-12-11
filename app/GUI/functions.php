<?php

namespace App\GUI;

use App\GUI\components\ISize;
use App\GUI\components\IOffset;
use App\GUI\components\IText;

function offset(?int $left = 0, ?int $top = 0): array
{
    return [IOffset::LEFT => $left, IOffset::TOP => $top];
}

function size(?int $width = 0, ?int $height = 0): array
{
    return [ISize::WIDTH => $width, ISize::HEIGHT => $height];
}

function text(?string $text = '', ?string $fontColor = null, ?int $fontSize = null): array
{
    return [IText::TEXT => $text, IText::FONT_COLOR => ($fontColor ?? Color::WHITE), IText::FONT_SIZE => ($fontSize ?? 10)];
}

function textAndColor(string $text, string $color, ?string $fontColor = null, ?int $fontSize = null): array
{
    $textArr = text($text, $fontColor, $fontSize);
    $textArr[Color::KEY] = $color;
    return $textArr;
}

function color(string $color): array
{
    return [Color::KEY => $color];
}

function sumTops(array $offset1, array $offset2 = null): array
{
    $offset1[IOffset::TOP] = $offset1[IOffset::TOP] + $offset2[IOffset::TOP];
    return $offset1;
}

function sumLefts(array $offset1, array $offset2 = null): array
{
    $offset1[IOffset::LEFT] = $offset1[IOffset::LEFT] + $offset2[IOffset::LEFT];
    return $offset1;
}

function toWidth(array &$sizes, int $value): array
{
    $sizes[ISize::WIDTH] = $value;
    return $sizes;
}

function height(?array $sizes): ?int
{
    return $sizes[ISize::HEIGHT]?? null;
}

function width(?array $sizes): ?int
{
    return $sizes[ISize::WIDTH] ?? null;
}

function setWidth(array $sizes, int $value): array
{
    $sizes[ISize::WIDTH] = $value;
    return $sizes;
}

function left(?array $offsets): ?int
{
    return $offsets[IOffset::LEFT] ?? null;
}

function top(?array $offsets): ?int
{
    return $offsets[IOffset::TOP] ?? null;
}

function getText(array $additions): ?string
{
    return $additions[IText::TEXT] ?? null;
}

function getFontColor(array $additions): ?string
{
    return $additions[IText::FONT_COLOR] ?? null;
}

function getFontSize(array $additions): ?string
{
    return $additions[IText::FONT_SIZE] ?? null;
}


function getColor(array $additions): ?string
{
    return $additions[Color::KEY] ?? null;
}


//arrays
function unsetKeys(array $arr, array $keys): array
{
    foreach ($keys as $key)
    {
        unset($arr[$key]);
    }
    return $arr;
}

function push(array $arr, array $keyValue): array
{
    $arr[$keyValue[array_key_first($keyValue)]] = $keyValue[0];
    return $arr;
}

function pushLeftToWidth(array $offsets, array $sizes): array
{
    $offsets[IOffset::LEFT] = $offsets[IOffset::LEFT] + $sizes[ISize::WIDTH];
    return $offsets;
}

function pushWidthToLeft(array $sizes, array $offsets): array
{
    $sizes[ISize::WIDTH] = $sizes[ISize::WIDTH] + $offsets[IOffset::LEFT];
    return $sizes;

}

function pullHeightToTop(array $sizes, array $offsets): array
{
    $sizes[ISize::HEIGHT] = $sizes[ISize::HEIGHT] - $offsets[IOffset::TOP];
    return $sizes;
}

function pullWidthToLeft(array &$sizes, array &$offsets, int $multiple = 1): array
{
    $sizes[ISize::WIDTH] = $sizes[ISize::WIDTH] - $multiple * $offsets[IOffset::LEFT];
    return $sizes;
}

function multipleTop(array $offsets, int $howMuch): array
{
    $offsets[IOffset::TOP] = $howMuch * $offsets[IOffset::TOP];
    return $offsets;
}

function inverseTop(array $offsets): array
{
    $offsets[IOffset::TOP] = -$offsets[IOffset::TOP];
    return $offsets;
}