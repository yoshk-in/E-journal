<?php


namespace App\GUI\components\computers;


use App\GUI\components\IOffset;
use App\GUI\components\ISize;
use function App\GUI\getFontSize;
use function App\GUI\getText;
use function App\GUI\height;
use function App\GUI\left;
use function App\GUI\sumLefts;
use function App\GUI\sumTops;
use function App\GUI\top;
use function App\GUI\width;

class SizeComputer
{
    public static function textInMiddle(array $offsets, array $sizes, array $additions): array
    {
        $word_width = round(0.73 * getFontSize($additions) * mb_strlen(getText($additions)));
        $word_height = 2 * getFontSize($additions);
        $offsets[IOffset::TOP] += (height($sizes) - $word_height) / 2;
        $offsets[IOffset::LEFT] += (width($sizes) - $word_width) / 2;
        return [$offsets, $sizes, $additions];
    }

    public static function inMiddle(array $offsets, array $sizes, array $additions): array
    {
        $padding = 10;
        $offsets[IOffset::TOP] += $padding;
        $sizes[ISize::WIDTH] -= 2 * $padding;
        $offsets[IOffset::LEFT] += $padding;
        return [$offsets, $sizes, $additions];
    }


    public static function plusOffsets(array $offsets, array $plusOffsets): array
    {
        $offsets = sumTops($offsets, $plusOffsets);
        return sumLefts($offsets, $plusOffsets);
    }

    public static function reduceHeightOn(array $sizes, int $on, ?int $multiple = 1): array
    {
        $sizes[ISize::HEIGHT] = height($sizes) - $multiple * $on;
        return $sizes;
    }


    public static function reduceWidthOn(array $sizes, int $on, ?int $multiple = 1): array
    {
        $sizes[ISize::WIDTH] = width($sizes) - $multiple * $on;
        return $sizes;
    }


    public static function increaseWidthOn(array $sizes, int $on, ?int $multiple = 1): array
    {
        $sizes[ISize::WIDTH] = width($sizes) + $multiple * $on;
        return $sizes;
    }

    public static function explodeWidthOn(array $sizes, int $on): array
    {
        $sizes[ISize::WIDTH] = width($sizes) / $on;
        return $sizes;
    }

    public static function reduceHeightAndIncreaseWidthOnOffset(array $sizes, array $offsets, int $multiple): array
    {
        $sizes[ISize::HEIGHT] = height($sizes) - $multiple * top($offsets);
        $sizes[ISize::WIDTH] = width($sizes) + $multiple * left($offsets);
        return $sizes;
    }

    public static function reduceSizesOnOffsets(array $sizes, array $offsets, int $multiple): array
    {
        $sizes[ISize::HEIGHT] = height($sizes) - $multiple * top($offsets);
        $sizes[ISize::WIDTH] = width($sizes) - $multiple * left($offsets);
        return $sizes;
    }
}